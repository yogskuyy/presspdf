#!/bin/bash
set -e

# Support dynamic PORT from cloud platforms (Railway, Render, Fly.io, Heroku)
PORT=${PORT:-80}
sed -i "s/listen 80 default_server;/listen ${PORT} default_server;/g" /etc/nginx/conf.d/default.conf || true
sed -i "s/listen \[::\]:80 default_server;/listen \[::\]:${PORT} default_server;/g" /etc/nginx/conf.d/default.conf || true

# Prepare storage directories
mkdir -p /var/www/html/storage/app/public/temp \
         /var/www/html/storage/app/temp_gs \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Fix ownership and permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force || true
fi

# Link storage
php artisan storage:link --force || true

# Run database migration automatically
php artisan migrate --force || true

# Cache configurations for maximum speed in production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "PressPDF production container initialized on port ${PORT}!"

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
