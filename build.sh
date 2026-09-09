#!/bin/bash
set -e

echo "Installing backend dependencies..."
cd backend
composer install --no-dev --optimize-autoloader

echo "Generating app key..."
php artisan key:generate --force

echo "Running migrations..."
touch database/database.sqlite
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --force

echo "Building frontend..."
cd ../frontend
npm install
npm run build

echo "Copying frontend build to backend public..."
cp -r dist/* ../backend/public/

echo "Clearing caches..."
cd ../backend
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Build complete!"
