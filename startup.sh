#!/bin/bash

# Konfiguration und Cache leeren
php artisan config:clear
php artisan cache:clear

# Migrationen ausführen
php artisan migrate --force

# PHP-FPM im Hintergrund starten
php-fpm -D

# Nginx im Vordergrund starten (nicht als Daemon)
nginx -g "daemon off;"
