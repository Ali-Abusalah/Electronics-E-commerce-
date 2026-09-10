FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    unzip \
    && docker-php-ext-install zip pdo pdo_sqlite gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

COPY . .

RUN php artisan key:generate --force || true
RUN touch database/database.sqlite
RUN php artisan migrate --force || true
RUN php artisan db:seed --force || true
RUN php artisan config:cache || true
RUN php artisan route:cache || true

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
