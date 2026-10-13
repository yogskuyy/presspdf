#!/bin/bash
# =========================================================
# PressPDF - 1-Click Ubuntu VPS Deployment Script
# Tested on Ubuntu 22.04 & 24.04 LTS
# =========================================================
set -e

echo "🚀 Memulai instalasi PressPDF di Ubuntu VPS..."

# 1. Update sistem & dependensi dasar
apt update && apt upgrade -y
apt install -y software-properties-common curl git unzip zip ghostscript nginx supervisor

# 2. Tambah PPA PHP 8.2 jika belum ada
add-apt-repository -y ppa:ondrej/php
apt update
apt install -y php8.2-fpm php8.2-cli php8.2-zip php8.2-gd php8.2-mbstring php8.2-xml php8.2-curl php8.2-bcmath

# 3. Install Composer
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# 4. Konfigurasi PHP.ini untuk upload 500MB
PHP_INI="/etc/php/8.2/fpm/php.ini"
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 500M/' $PHP_INI
sed -i 's/post_max_size = .*/post_max_size = 500M/' $PHP_INI
sed -i 's/memory_limit = .*/memory_limit = 512M/' $PHP_INI
sed -i 's/max_execution_time = .*/max_execution_time = 600/' $PHP_INI
systemctl restart php8.2-fpm

# 5. Permission storage & bootstrap cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 6. Composer install & Laravel cache
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan storage:link --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ PressPDF berhasil disiapkan! Silakan arahkan Nginx vhost ke folder public/."
