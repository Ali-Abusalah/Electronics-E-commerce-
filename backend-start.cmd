@echo off
title DCTech Backend
cd /d "C:\Users\ALI.A.SALAH\Desktop\Front-End Electronics E-commerce Website me\backend\public"
echo ==== start %DATE% %TIME% ==== >> "%TEMP%\dctech-backend.log"
php -d max_execution_time=300 -d memory_limit=512M -d opcache.enable=1 -S 0.0.0.0:8000 -t . "..\vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php" >> "%TEMP%\dctech-backend.log" 2>&1
echo ==== exited with %ERRORLEVEL% %DATE% %TIME% ==== >> "%TEMP%\dctech-backend.log"
