@echo off
rem Keep DCTech Shop servers alive: restarts whichever one stopped listening.
netstat -ano | findstr /r /c:"TCP.*:8000 .*LISTENING" >nul
if errorlevel 1 (
    echo %DATE% %TIME% backend down - restarting >> "%TEMP%\dctech-watchdog.log"
    schtasks /run /tn "DCTech Backend" >nul 2>&1
)
netstat -ano | findstr /r /c:"TCP.*:5173 .*LISTENING" >nul
if errorlevel 1 (
    echo %DATE% %TIME% frontend down - restarting >> "%TEMP%\dctech-watchdog.log"
    schtasks /run /tn "DCTech Frontend" >nul 2>&1
)
