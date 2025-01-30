#!/bin/bash

echo "Starting deployment..."

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

# Check Cloud Storage for existing database
echo "Checking Cloud Storage bucket: $GOOGLE_CLOUD_STORAGE_BUCKET"
if gsutil ls gs://$GOOGLE_CLOUD_STORAGE_BUCKET/database.sqlite 2>/dev/null; then
    echo "Found existing database, downloading..."
    gsutil cp gs://$GOOGLE_CLOUD_STORAGE_BUCKET/database.sqlite /var/www/database/database.sqlite
    if [ $? -ne 0 ]; then
        echo "Error downloading database from Cloud Storage"
        exit 1
    fi
else
    echo "No existing database found in Cloud Storage"
fi

# Create or update database
if [ ! -f "/var/www/database/database.sqlite" ]; then
    echo "Creating new database..."
    touch /var/www/database/database.sqlite
    chmod 664 /var/www/database/database.sqlite
    chown www-data:www-data /var/www/database/database.sqlite

    echo "Running initial migration and seeding..."
    php artisan migrate --force
    if [ $? -ne 0 ]; then
        echo "Migration failed"
        exit 1
    fi

    php artisan db:seed --force
    if [ $? -ne 0 ]; then
        echo "Seeding failed"
        exit 1
    fi

    echo "Uploading new database to Cloud Storage..."
    gsutil cp /var/www/database/database.sqlite gs://$GOOGLE_CLOUD_STORAGE_BUCKET/database.sqlite
    if [ $? -ne 0 ]; then
        echo "Error uploading database to Cloud Storage"
        exit 1
    fi
else
    echo "Running migrations on existing database..."
    php artisan migrate --force
fi

echo "Creating storage link..."
php artisan storage:link --force

echo "Optimizing application..."
php artisan optimize

echo "Starting PHP-FPM..."
php-fpm

echo "Starting Nginx..."
exec nginx -g 'daemon off;'
