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
    && mkdir -p /var/www/html/database

# SQLite Datenbank erstellen
RUN touch /var/www/html/database/database.sqlite \
    && chmod 666 /var/www/html/database/database.sqlite \
    && chown www-data:www-data /var/www/html/database/database.sqlite \
    && chmod 777 /var/www/html/database

# Berechtigungen setzen
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html/storage -type f -exec chmod 664 {} \; \
    && find /var/www/html/storage -type d -exec chmod 775 {} \; \
    && find /var/www/html/bootstrap/cache -type f -exec chmod 664 {} \; \
    && find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \;

# PHP-FPM Konfiguration anpassen
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 9000/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/user = www-data/user = root/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/group = www-data/group = root/' /usr/local/etc/php-fpm.d/www.conf

# Nginx Konfiguration
RUN echo 'server { \
    listen 8080; \
    listen [::]:8080; \
    server_name _; \
    root /var/www/html/public; \
    index index.php; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
}' > /etc/nginx/sites-available/default

# Startup script
COPY startup.sh /startup.sh
RUN chmod +x /startup.sh

# Environment variables
ENV PORT=8080
ENV NGINX_PORT=8080

# Remove default nginx configuration
RUN rm /etc/nginx/sites-enabled/default
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# USER root für die Container-Ausführung
USER root

# Default command
CMD ["/startup.sh"]

EXPOSE 8080
