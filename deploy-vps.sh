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
apt install -y php8.2-fpm php8.2-cli php8.2-zip php8.2-gd php8.2-mbstring php8.2-xml php8.2-curl php8.2-bcmath php8.2-mysql php8.2-sqlite3

# 3. Install Composer
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# 4. Konfigurasi PHP.ini untuk upload hingga 1 GB (1024M) & batas waktu 900 detik
for PHP_INI in "/etc/php/8.2/fpm/php.ini" "/etc/php/8.2/cli/php.ini"; do
    if [ -f "$PHP_INI" ]; then
        sed -i 's/upload_max_filesize = .*/upload_max_filesize = 1024M/' "$PHP_INI"
        sed -i 's/post_max_size = .*/post_max_size = 1024M/' "$PHP_INI"
        sed -i 's/memory_limit = .*/memory_limit = 1024M/' "$PHP_INI"
        sed -i 's/max_execution_time = .*/max_execution_time = 900/' "$PHP_INI"
    fi
done
systemctl restart php8.2-fpm || true

# 5. Konfigurasi Nginx global client_max_body_size agar berkas besar tidak terblokir
mkdir -p /etc/nginx/conf.d
cat << 'EOF' > /etc/nginx/conf.d/presspdf_upload.conf
client_max_body_size 1024M;
client_body_timeout 900s;
EOF
systemctl reload nginx || true

# 6. Salin file .env jika belum ada
if [ ! -f .env ]; then
    echo "📋 Menyalin .env.example menjadi .env..."
    cp .env.example .env
fi

# 7. Siapkan direktori storage & permissions
mkdir -p storage/app/public/temp \
         storage/app/temp_gs \
         storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 8. Composer install & Laravel cache
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan storage:link --force
php artisan migrate --force || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "=========================================================="
echo "✅ PressPDF berhasil disiapkan di server Linux VPS!"
echo "👉 File template Nginx VPS tersedia di: docker/nginx-vps.conf"
echo "=========================================================="
