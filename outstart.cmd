@echo off
title DCTech Shop - Auto Start
cd /d "C:\Users\ALI.A.SALAH\Desktop\Front-End Electronics E-commerce Website me\backend"
php artisan config:clear 2>nul
php artisan route:clear 2>nul
php artisan cache:clear 2>nul
start "DCTech Backend" php artisan serve --host=0.0.0.0 --port=8000
cd /d "C:\Users\ALI.A.SALAH\Desktop\Front-End Electronics E-commerce Website me\frontend"
start "DCTech Frontend" npm run dev -- --host
