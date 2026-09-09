@echo off
title DCTech Shop - Servers
color 0A
echo.
echo  ======================================
echo   DCTech Shop - Starting Servers
echo  ======================================
echo.

cd /d "%~dp0"

:: Start Backend
echo [1/2] Starting Backend on http://192.168.0.108:8000
start "Backend" /MIN cmd /c "cd /d "%~dp0backend" && php artisan serve --host=0.0.0.0 --port=8000"

:: Wait for backend
timeout /t 3 /nobreak >nul

:: Start Frontend
echo [2/2] Starting Frontend on http://192.168.0.108:5173
start "Frontend" /MIN cmd /c "cd /d "%~dp0frontend" && npm run dev -- --host"

:: Get IP
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr /v "127"') do set LOCALIP=%%a
set LOCALIP=%LOCALIP: =%

echo.
echo  ======================================
echo   Servers are running!
echo.
echo   Local:    http://localhost:5173
echo   Network:  http://%LOCALIP%:5173
echo   Admin:    http://127.0.0.1:8000
echo  ======================================
echo.
echo  Close this window or press any key to exit.
echo  Servers will keep running in the background.
echo.
pause >nul
