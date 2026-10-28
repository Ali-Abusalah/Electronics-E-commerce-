@echo off
rem DCTech Backend — Laravel server on port 8000. Safe to run many times:
rem if 8000 is already listening, this exits without doing anything.
netstat -ano | findstr /r /c:"TCP.*:8000 .*LISTENING" >nul
if errorlevel 1 (
    cd /d "C:\Users\ALI.A.SALAH\Desktop\projects\Front-End Electronics E-commerce Website me\backend\public"
    if errorlevel 1 ( echo ==== cd to backend\public FAILED %DATE% %TIME% ==== >> "%TEMP%\dctech-backend.log" & exit /b 1 )
    echo ==== start %DATE% %TIME% ==== >> "%TEMP%\dctech-backend.log"
    php -d max_execution_time=300 -d memory_limit=512M -d opcache.enable=1 -S 0.0.0.0:8000 -t . "..\vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php" >> "%TEMP%\dctech-backend.log" 2>&1
    echo ==== exited with %ERRORLEVEL% %DATE% %TIME% ==== >> "%TEMP%\dctech-backend.log"
) else (
    echo ==== already running %DATE% %TIME% ==== >> "%TEMP%\dctech-backend.log"
)
