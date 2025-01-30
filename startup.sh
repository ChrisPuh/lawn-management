#!/bin/bash

# Berechtigungen setzen
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/database
chmod -R 775 /var/www/database

# Prüfe ob Datenbank existiert
if [ ! -f "/var/www/database/database.sqlite" ]; then
    touch /var/www/database/database.sqlite
    chmod 664 /var/www/database/database.sqlite
    chown www-data:www-data /var/www/database/database.sqlite

    # Nur bei neuer DB migrieren und seeden
    php artisan migrate --force
    php artisan db:seed --force
else
    # Bei existierender DB nur migrieren
    php artisan migrate --force
fi

# Storage Link erstellen
php artisan storage:link --force

# Cache leeren und neu generieren
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# PHP-FPM starten
php-fpm

# Nginx starten
nginx -g "daemon off;"
