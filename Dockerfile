# =========================================================
# PressPDF Production Dockerfile
# Optimized for Laravel + Native Ghostscript + Nginx + PHP-FPM
# =========================================================
FROM php:8.2-fpm-bullseye

# Set environment
ENV DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies & Ghostscript
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    ghostscript \
    supervisor \
    curl \
    git \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        zip \
        gd \
        bcmath \
        pcntl \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Copy configurations
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set permissions for entrypoint
RUN chmod +x /var/www/html/docker/entrypoint.sh

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Create storage & bootstrap cache directories and fix permissions
RUN mkdir -p storage/app/public/temp \
             storage/app/temp_gs \
             storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose HTTP port (Railway / Render will map dynamically via $PORT)
EXPOSE 80

# Run entrypoint
ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
