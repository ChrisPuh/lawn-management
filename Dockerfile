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

# Startup-Skript kopieren und Rechte setzen
COPY startup.sh /startup.sh
RUN chmod +x /startup.sh

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

# Nginx Konfiguration
RUN echo 'server { \
    listen 8080; \
    server_name _; \
    root /var/www/html/public; \
    index index.php; \
    charset utf-8; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location = /favicon.ico { access_log off; log_not_found off; } \
    location = /robots.txt  { access_log off; log_not_found off; } \
    error_page 404 /index.php; \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
}' > /etc/nginx/sites-available/default

# PHP-FPM Konfiguration anpassen
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 9000/' /usr/local/etc/php-fpm.d/www.conf

# CMD für Startup-Skript
CMD ["/startup.sh"]
