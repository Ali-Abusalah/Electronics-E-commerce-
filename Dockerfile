FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    curl \
    && docker-php-ext-install zip pdo pdo_sqlite gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY backend/composer.json backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY frontend/package.json frontend/package-lock.json ./frontend/
RUN cd frontend && npm install

COPY backend/ ./backend/
COPY frontend/ ./frontend/

RUN cd frontend && npm run build
RUN cp -r frontend/dist/* backend/public/

RUN cd backend && php artisan key:generate --force || true
RUN cd backend && php artisan config:cache || true
RUN cd backend && php artisan route:cache || true
RUN cd backend && touch database/database.sqlite
RUN cd backend && php artisan migrate --force || true
RUN cd backend && php artisan db:seed --force || true

WORKDIR /app/backend

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
