#!/bin/bash

# Hentikan eksekusi script segera jika ada perintah yang gagal (Exit on Error)
set -e

# Fungsi untuk mencetak log dengan stempel waktu (opsional, untuk debugging)
log_message() {
    echo "[INFO] $1"
}

log_message "Starting container initialization..."

# 1. Cek & Install Dependensi PHP (Composer)
if [ ! -d "vendor" ]; then
    log_message "Vendor directory not found. Running composer install..."
    composer install --no-interaction --optimize-autoloader
else
    log_message "Composer dependencies already installed."
fi

# 2. Cek & Install Dependensi JS (NPM)
if [ ! -d "node_modules" ]; then
    log_message "Node modules directory not found. Running npm install..."
    npm install
else
    log_message "NPM dependencies already installed."
fi

# 3. Setup File .env
if [ ! -f ".env" ]; then
    log_message "Environment file (.env) not found. Copying from .env.example..."
    cp .env.example .env
    php artisan key:generate
fi

# 4. Setup Izin Folder (Permission)
log_message "Setting permissions for storage and cache..."
chmod -R 777 storage bootstrap/cache # sangat tidak aman untuk Production

# 5. Migrasi Database
log_message "Waiting for database connection..."
sleep 5

log_message "Running database migrations..."
php artisan migrate --force

# 6. Jalankan Perintah Utama (Apache)
log_message "Initialization complete. Starting Apache..."
exec "$@"