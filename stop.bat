@echo off
echo Stopping DCTech Shop servers...
echo.

echo Stopping PHP processes...
taskkill /F /IM php.exe /T 2>nul
if %errorlevel%==0 (echo PHP stopped.) else (echo No PHP processes found.)

echo Stopping Node processes...
taskkill /F /IM node.exe /T 2>nul
if %errorlevel%==0 (echo Node stopped.) else (echo No Node processes found.)

echo.
echo ========================================
echo  All servers stopped!
echo ========================================
echo.
pause
