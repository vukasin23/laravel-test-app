FROM php:8.2-fpm

# Sistem zavisnosti
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Radni direktorijum
WORKDIR /var/www/html

# Kopiranje svega
COPY . .

# Instalacija Composer paketa (bez --no-dev da ne pukne)
RUN curl -sS https://getcomposer.org/installer | php && \
    php composer.phar install --optimize-autoloader --no-scripts --no-interaction || true

# Instalacija Node paketa i build
RUN npm install && npm run build

# Čišćenje keša i pokretanje migracija
RUN php artisan config:clear && \
    php artisan cache:clear && \
    php artisan view:clear && \
    php artisan route:clear && \
    php artisan migrate --force || true

# Pokretanje Laravel servera
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
