#!/bin/bash

# Konfiguration und Cache leeren
php artisan config:clear
php artisan cache:clear

# Migrationen ausführen
php artisan migrate --force

# Datenbank seeden
php artisan db:seed --force

# PHP-FPM im Hintergrund starten
php-fpm -D

# Nginx im Vordergrund starten
nginx -g "daemon off;"
