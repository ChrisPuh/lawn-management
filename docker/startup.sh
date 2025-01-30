#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# Enable verbose output
set -x

echo "Starting deployment with enhanced logging..."

# Print environment variables for debugging
env

# Check Cloud Storage bucket access
echo "Checking Cloud Storage bucket access..."
if gsutil ls gs://$GOOGLE_CLOUD_STORAGE_BUCKET 2>/dev/null; then
    echo "Successfully accessed bucket: $GOOGLE_CLOUD_STORAGE_BUCKET"
else
    echo "ERROR: Cannot access Cloud Storage bucket $GOOGLE_CLOUD_STORAGE_BUCKET"
    # Attempt to list buckets to diagnose permissions
    gsutil ls
fi

# Replace environment variables in nginx config
envsubst '$PORT' < /etc/nginx/sites-available/default > /etc/nginx/sites-enabled/default

# Set permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
mkdir -p /var/www/database
chown -R www-data:www-data /var/www/database
chmod -R 775 /var/www/database

# Extended database initialization logging
echo "Database Initialization Process..."

# Check database file path
DATABASE_PATH="/var/www/database/database.sqlite"
echo "Expected Database Path: $DATABASE_PATH"

# Check if database exists
if [ ! -f "$DATABASE_PATH" ]; then
    echo "No existing database found. Creating new database..."
    touch "$DATABASE_PATH"
    chmod 664 "$DATABASE_PATH"
    chown www-data:www-data "$DATABASE_PATH"
fi

# Verbose database operations
echo "Running database migrations..."
php artisan migrate --force --verbose

echo "Running database seeding..."
php artisan db:seed --force --verbose

# Attempt to upload database to Cloud Storage (with more logging)
echo "Attempting to upload database to Cloud Storage..."
if gsutil cp "$DATABASE_PATH" "gs://$GOOGLE_CLOUD_STORAGE_BUCKET/database.sqlite" 2>&1; then
    echo "Successfully uploaded database to Cloud Storage"
else
    echo "WARNING: Failed to upload database to Cloud Storage"
    # Print detailed gsutil error information
    gsutil cp "$DATABASE_PATH" "gs://$GOOGLE_CLOUD_STORAGE_BUCKET/database.sqlite"
fi

echo "Creating storage link..."
php artisan storage:link --force

echo "Optimizing application..."
php artisan optimize

echo "Starting PHP-FPM..."
php-fpm &

echo "Starting Nginx..."
exec nginx -g 'daemon off;'
