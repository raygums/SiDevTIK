# STAGE 1: Build Backend Dependencies (Composer)
FROM composer:2.6 as vendor-build
WORKDIR /app

# Copy composer files saja dulu untuk memanfaatkan Docker Cache
COPY composer.json composer.lock ./

# Install dependensi produksi (tanpa dev-dependencies seperti faker/phpunit)
RUN composer install \
    --no-dev \
    --ignore-platform-reqs \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# STAGE 2: Build Frontend Assets (Node.js + Vite)
FROM node:20-alpine as frontend-build
WORKDIR /app

# Copy package files
COPY package.json package-lock.json ./

# Clean Install (ci) - Lebih ketat dan cepat untuk CI/CD
RUN npm ci

# Copy seluruh source code (karena Vite butuh scan file Blade)
COPY . .

# Build assets (hasilnya ada di public/build)
RUN npm run build

# STAGE 3: Production Image (PHP-FPM)
FROM php:8.2-fpm-alpine

# Install ekstensi sistem yang dibutuhkan Laravel & Postgres
# libpng-dev, libpq-dev, zip, unzip sesuai kebutuhan Anda
RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    acl \
    fcgi \
    && docker-php-ext-install pdo_pgsql zip bcmath gd opcache

# Konfigurasi PHP Production (Optimasi Opcache)
COPY docker/production/php.ini /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

# 1. Copy Vendor dari Stage 1
COPY --from=vendor-build /app/vendor ./vendor

# 2. Copy Assets (CSS/JS) dari Stage 2
COPY --from=frontend-build /app/public/build ./public/build

# 3. Copy Source Code Aplikasi
COPY . .

# 4. Setup Permission
# Kita pastikan www-data (user default PHP-FPM) memiliki akses
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copy Entrypoint Production
COPY docker/production/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]