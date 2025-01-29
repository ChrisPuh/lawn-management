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
    libicu-dev

# PHP-Erweiterungen
RUN docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd zip
RUN docker-php-ext-configure intl && docker-php-ext-install intl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm

# Anwendungsdateien kopieren
COPY . .

# Storage directory vorbereiten
RUN mkdir -p storage/app/public
RUN mkdir -p public/storage
RUN chmod -R 775 storage public/storage
RUN chown -R www-data:www-data storage public/storage

# Startup-Skript kopieren und Rechte setzen
COPY startup.sh /startup
