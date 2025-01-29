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

# Berechtigungen setzen
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Abhängigkeiten installieren
RUN composer install --no-dev --optimize-autoloader
RUN npm ci && npm run build

# SQLite-Datenbank vorbereiten
RUN mkdir -p database && \
    touch /var/www/html/database/database.sqlite && \
    chmod 775 /var/www/html/database/database.sqlite && \
    chown www-data:www-data /var/www/html/database/database.sqlite

# Startup-Skript
RUN echo '#!/bin/bash' > /startup.sh && \
    echo 'php artisan config:clear' >> /startup.sh && \
    echo 'php artisan cache:clear' >> /startup.sh && \
    echo 'php artisan migrate --force' >> /startup.sh && \
    echo 'php-fpm -F' >> /startup.sh && \
    chmod +x /startup.sh

# Port
EXPOSE 8080

# CMD
CMD ["/startup.sh"]
