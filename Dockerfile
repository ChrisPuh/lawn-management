FROM php:8.4-fpm

WORKDIR /var/www/html

# System-Abhängigkeiten
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    libsqlite3-dev \
    sqlite3 \
    libzip-dev \
    libicu-dev \
    supervisor

# PHP-Erweiterungen
RUN docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd zip
RUN docker-php-ext-configure intl && docker-php-ext-install intl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm

# Composer files kopieren
COPY composer.json composer.lock ./

# Composer Dependencies installieren
RUN composer install --no-dev --no-scripts --no-autoloader

# Package.json kopieren und NPM Dependencies installieren
COPY package.json package-lock.json ./
RUN npm ci

# Restliche Anwendungsdateien kopieren
COPY . .

# Composer Autoloader optimieren
RUN composer dump-autoload --optimize

# Assets bauen
RUN npm run build

# Nginx Konfiguration
COPY docker/nginx.conf /etc/nginx/nginx.conf
RUN rm -rf /etc/nginx/sites-enabled/* /etc/nginx/sites-available/*

# Supervisor Konfiguration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Storage und Bootstrap Cache vorbereiten
RUN mkdir -p /var/www/html/storage/logs \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/testing \
    /var/www/html/storage/framework/views \
    /var/www/html/bootstrap/cache \
    /var/www/database \
    /run/php

# Berechtigungen setzen
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/database \
    && chmod -R 775 /var/www/database

ENV PORT=8080
EXPOSE 8080

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
