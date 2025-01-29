FROM php:8.4-fpm

WORKDIR /var/www/html

# Install system dependencies
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

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd zip
RUN docker-php-ext-configure intl && docker-php-ext-install intl

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm

# Copy application files
COPY . .

# Set correct permissions
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Install dependencies
RUN composer install --no-dev --optimize-autoloader
RUN npm ci && npm run build

# Generate application key
RUN php artisan key:generate

# Create SQLite database if not exists
RUN touch database/database.sqlite \
    && chmod 775 database/database.sqlite \
    && chown www-data:www-data database/database.sqlite

# Use Nginx or PHP-FPM with a startup script
RUN echo '#!/bin/bash' > /startup.sh \
    && echo 'php artisan migrate --force' >> /startup.sh \
    && echo 'php-fpm -F' >> /startup.sh \
    && chmod +x /startup.sh

# Expose port
EXPOSE 8080

# Use a startup script
CMD ["/startup.sh"]
