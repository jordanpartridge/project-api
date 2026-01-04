FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer.json
COPY composer.json composer.lock ./

# Set permissions for composer
RUN chmod 755 /usr/bin/composer

# Install composer dependencies
RUN composer install --no-scripts --no-autoloader --no-interaction

# Copy existing application directory contents
COPY . .

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Generate autoload files
RUN composer dump-autoload --optimize

# Install npm dependencies
RUN npm install
RUN npm run build

# Generate application key
RUN php artisan key:generate --force

EXPOSE 9000
CMD ["php-fpm"]