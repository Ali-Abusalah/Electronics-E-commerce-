@echo off
title DCTech Shop - Auto Startup
cd /d "%~dp0backend"
start "Backend" /MIN php artisan serve --host=0.0.0.0 --port=8000
cd /d "%~dp0frontend"
start "Frontend" /MIN npm run dev -- --host
