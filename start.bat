@echo off
title DCTech Shop
echo Starting DCTech Shop...
echo.

start "DCTech Backend" "%~dp0backend-start.cmd"
start "DCTech Frontend" "%~dp0frontend-start.cmd"

echo Servers started!
echo Frontend: http://localhost:5173
echo Backend:  http://localhost:8000
echo.
echo Close this window. Servers run in background.
timeout /t 5
