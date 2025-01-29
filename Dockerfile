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

# Restliche Anwendungsdateien kopieren
COPY . .

# Composer Autoloader optimieren
RUN composer dump-autoload --optimize

# Storage directory vorbereiten
RUN mkdir -p storage/app/public
RUN mkdir -p public/storage
RUN chmod -R 775 storage public/storage
RUN chown -R www-data:www-data storage public/storage

# PHP-FPM Konfiguration anpassen
RUN sed -i 's/listen = 127.0.0.1:9000/listen = 9000/' /usr/local/etc/php-fpm.d/www.conf

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

# Berechtigungen setzen
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Default command
CMD ["/startup.sh"]

EXPOSE 8080
