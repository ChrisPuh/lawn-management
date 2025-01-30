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

# Storage und Bootstrap Cache vorbereiten
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/testing \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/database

# Berechtigungen setzen
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/database \
    && chmod -R 775 /var/www/database

# Nginx Konfiguration
COPY docker/default.conf /etc/nginx/sites-available/default
RUN ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# PHP-FPM Konfiguration
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# Umgebungsvariablen
ENV PORT=8080
ENV NGINX_PORT=8080

# Startup script
COPY docker/startup.sh /startup.sh
RUN chmod +x /startup.sh

CMD ["/startup.sh"]

EXPOSE 8080
