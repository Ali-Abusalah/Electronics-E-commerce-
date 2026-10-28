@echo off
title DCTech Backend
cd /d "C:\Users\ALI.A.SALAH\Desktop\Front-End Electronics E-commerce Website me\backend"
if not exist "%TEMP%\php-opcache" mkdir "%TEMP%\php-opcache" 2>nul
echo ==== start %DATE% %TIME% ==== >> "%TEMP%\dctech-backend.log"
php -d max_execution_time=300 -d memory_limit=512M artisan serve --host=0.0.0.0 --port=8000 >> "%TEMP%\dctech-backend.log" 2>&1
echo ==== exited with %ERRORLEVEL% %DATE% %TIME% ==== >> "%TEMP%\dctech-backend.log"
