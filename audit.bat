@echo off
title DCTech Shop - Network Audit
color 0F
echo.
echo  =============================================
echo   DCTech Shop - Network Audit Script
echo  =============================================
echo.

:: ---- 1. System Info ----
echo [1] SYSTEM INFO
echo -----------------------------------------------
echo Computer: %COMPUTERNAME%
echo User:     %USERNAME%
echo Date:     %date% %time%
echo.

:: ---- 2. IP Addresses ----
echo [2] IP ADDRESSES
echo -----------------------------------------------
ipconfig | findstr /i "IPv4"
echo.

:: ---- 3. Check Servers ----
echo [3] SERVER STATUS
echo -----------------------------------------------
echo Checking port 8000 (Backend)...
netstat -ano | findstr ":8000" | findstr "LISTENING" >nul 2>&1
if %errorlevel%==0 (
    echo   [OK] Backend is RUNNING on port 8000
) else (
    echo   [FAIL] Backend is NOT running on port 8000
)

echo Checking port 5173 (Frontend)...
netstat -ano | findstr ":5173" | findstr "LISTENING" >nul 2>&1
if %errorlevel%==0 (
    echo   [OK] Frontend is RUNNING on port 5173
) else (
    echo   [FAIL] Frontend is NOT running on port 5173
)
echo.

:: ---- 4. Check if ports listen on 0.0.0.0 ----
echo [4] PORT BINDING
echo -----------------------------------------------
echo Port 8000 binding:
netstat -ano | findstr ":8000" | findstr "LISTENING"
echo.
echo Port 5173 binding:
netstat -ano | findstr ":5173" | findstr "LISTENING"
echo.

:: ---- 5. Test Backend API ----
echo [5] BACKEND API TEST
echo -----------------------------------------------
echo Testing http://127.0.0.1:8000/api/products ...
curl -s -o nul -w "  Status: %%{http_code}\n" http://127.0.0.1:8000/api/products 2>nul
if %errorlevel% neq 0 (
    echo   [FAIL] Cannot reach backend API on localhost
)
echo.

:: ---- 6. Firewall Rules ----
echo [6] FIREWALL RULES (DCTech)
echo -----------------------------------------------
netsh advfirewall firewall show rule name=all dir=in | findstr /i "DCTech"
if %errorlevel% neq 0 (
    echo   [WARN] No DCTech firewall rules found!
    echo   Run "open-ports.bat" as Administrator to fix.
)
echo.

:: ---- 7. Test Network Access ----
echo [7] NETWORK ACCESS TEST
echo -----------------------------------------------
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr /v "127" ^| findstr /v "169.254"') do set LOCALIP=%%a
set LOCALIP=%LOCALIP: =%
echo Your IP: %LOCALIP%
echo.
echo Testing http://%LOCALIP%:8000/api/products ...
curl -s -o nul -w "  Status: %%{http_code}\n" --connect-timeout 5 http://%LOCALIP%:8000/api/products 2>nul
if %errorlevel% neq 0 (
    echo   [FAIL] Cannot reach backend on network IP
    echo   This means firewall is blocking or port binding is wrong.
)
echo.

:: ---- 8. Processes ----
echo [8] PHP AND NODE PROCESSES
echo -----------------------------------------------
tasklist | findstr /i "php.exe"
if %errorlevel% neq 0 echo   [WARN] No PHP processes found
tasklist | findstr /i "node.exe"
if %errorlevel% neq 0 echo   [WARN] No Node processes found
echo.

:: ---- 9. CORS Config ----
echo [9] CORS CONFIG
echo -----------------------------------------------
echo Checking backend CORS config...
findstr "allowed_origins" "C:\Users\ALI.A.SALAH\Desktop\Front-End Electronics E-commerce Website me\backend\config\cors.php"
echo.

:: ---- 10. Sanctum Config ----
echo [10] SANCTUM STATEFUL DOMAINS
echo -----------------------------------------------
findstr "SANCTUM_STATEFUL_DOMAINS" "C:\Users\ALI.A.SALAH\Desktop\Front-End Electronics E-commerce Website me\backend\.env"
echo.

echo  =============================================
echo   Audit Complete!
echo  =============================================
echo.
pause
