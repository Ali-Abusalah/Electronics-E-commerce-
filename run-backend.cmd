@echo off
cd /d "C:\Users\ALI.A.SALAH\Desktop\Front-End Electronics E-commerce Website me\backend"
php artisan config:clear 2>nul
if not exist "%TEMP%\php-opcache" mkdir "%TEMP%\php-opcache" 2>nul
php -d max_execution_time=300 -d opcache.enable_cli=1 -d opcache.file_cache="%TEMP%\php-opcache" artisan serve --host=0.0.0.0 --port=8000
