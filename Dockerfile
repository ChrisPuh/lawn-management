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

# Copy application
COPY . .

# Install dependencies with npm ci for consistent builds
RUN composer install --no-dev --optimize-autoloader
RUN npm ci && npm run build

# Create SQLite database
RUN touch database/database.sqlite \
    && chmod -R 775 storage bootstrap/cache database public/build \
    && chown -R www-data:www-data storage bootstrap/cache database public/build

# Ensure Vite assets are linked
RUN php artisan storage:link

EXPOSE 8080
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
