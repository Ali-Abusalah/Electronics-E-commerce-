@echo off
echo ============================================
echo   DCTech Shop - Install Auto Start
echo ============================================
echo.

:: Register Backend task
schtasks /create /tn "DCTech Backend" /tr "C:\Users\ALI.A.SALAH\dctech-backend.cmd" /sc onlogon /rl highest /f
echo [OK] Backend task registered

:: Register Frontend task  
schtasks /create /tn "DCTech Frontend" /tr "C:\Users\ALI.A.SALAH\dctech-frontend.cmd" /sc onlogon /rl highest /f
echo [OK] Frontend task registered

echo.
echo ============================================
echo   Done! Servers will start on every login.
echo   Mobile: http://192.168.0.108:5173
echo ============================================
echo.
pause
