# Step 1: Build assets with Node
FROM node:18-alpine AS node

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

COPY resources/ resources/
COPY vite.config.js .
RUN npm run build

# Step 2: Setup PHP + Composer + Copy built assets
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Copy ONLY the built assets from node stage
COPY --from=node /app/public/build /var/www/html/public/build

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000
