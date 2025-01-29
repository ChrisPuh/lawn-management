#!/bin/bash

# Laravel Storage Link erstellen
php artisan storage:link --force

# Cache leeren und neu generieren
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migrationen ausführen
php artisan migrate --force

# Datenbank seeden
php artisan db:seed --force

# PHP-FPM starten
php-fpm -D

# Nginx im Vordergrund starten
nginx -g "daemon off;"
