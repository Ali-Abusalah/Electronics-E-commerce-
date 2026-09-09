@echo off
cd /d "C:\Users\ALI.A.SALAH\Desktop\Front-End Electronics E-commerce Website me\backend"
php artisan config:clear 2>nul
php artisan serve --host=0.0.0.0 --port=8000
