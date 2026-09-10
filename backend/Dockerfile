FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY backend/ .

RUN composer dump-autoload --optimize
RUN php artisan key:generate --force || true
RUN php artisan config:cache || true
RUN php artisan route:cache || true

RUN touch database/database.sqlite
RUN php artisan migrate --force || true
RUN php artisan db:seed --force || true

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
