FROM php:8.4-fpm

ARG WWWGROUP
ARG WWWUSER

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
    libzip-dev \
    libicu-dev \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions properly
RUN pecl install zip && \
    docker-php-ext-enable zip

RUN docker-php-ext-configure intl && \
    docker-php-ext-install intl

RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Verify installations
RUN php -m | grep -q 'zip' && echo "zip installed" || echo "zip not installed"
RUN php -m | grep -q 'intl' && echo "intl installed" || echo "intl not installed"

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user
RUN useradd -G www-data,root -u $WWWUSER -d /home/$WWWUSER $WWWUSER
RUN mkdir -p /home/$WWWUSER/.composer && \
    chown -R $WWWUSER:$WWWUSER /home/$WWWUSER

# Install Node.js and npm
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get update && apt-get install -y nodejs && \
    npm install -g npm

# Set working directory permission
RUN chown -R $WWWUSER:$WWWUSER /var/www/html

USER $WWWUSER
