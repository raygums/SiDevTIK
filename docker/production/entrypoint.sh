#!/bin/sh
set -e

echo "Starting Production Entrypoint..."

if [ -d "/var/www/html/storage" ]; then
    chown -R www-data:www-data /var/www/html/storage
fi

# 1. Caching Configuration (Wajib di Production untuk Performa)
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 2. Setup Storage Link (Agar file upload bisa diakses publik)
if [ ! -L public/storage ]; then
    echo "Creating storage link..."
    php artisan storage:link
fi

# 3. Database Migration
# HATI-HATI: Gunakan --force karena di production Laravel meminta konfirmasi yes/no
echo "Running migrations..."
php artisan migrate --force

echo "Ready to serve requests."

# Jalankan command (php-fpm)
exec "$@"