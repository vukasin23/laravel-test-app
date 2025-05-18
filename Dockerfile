# 1. Stage: Build assets using Node
FROM node:18-alpine AS node

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

COPY resources/ resources/
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build


# 2. Stage: Install PHP dependencies
FROM composer:latest AS vendor

COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-scripts --no-progress

# 3. Stage: Final application container
FROM php:8.2-fpm

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copy built assets and vendor
COPY --from=node /app/public/build /var/www/html/public/build
COPY --from=vendor /app/vendor /var/www/html/vendor

# Copy full Laravel source
COPY . .

# Fix permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# === CLEAN CACHE AND FORCE HTTPS ===
RUN php artisan config:clear \
 && php artisan cache:clear \
 && php artisan view:clear \
 && php artisan route:clear \
 && php artisan migrate --force || true

# Start Laravel server (for Railway or Docker local)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
