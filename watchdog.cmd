@echo off
rem Keep DCTech Shop servers alive: restarts whichever one stopped listening.
netstat -ano | findstr /r /c:"TCP.*:8000 .*LISTENING" >nul
if errorlevel 1 (
    echo %DATE% %TIME% backend down - restarting >> "%TEMP%\dctech-watchdog.log"
    wscript "C:\Users\ALI.A.SALAH\Desktop\projects\Front-End Electronics E-commerce Website me\start-backend-hidden.vbs" >nul 2>&1
)
netstat -ano | findstr /r /c:"TCP.*:5173 .*LISTENING" >nul
if errorlevel 1 (
    echo %DATE% %TIME% frontend down - restarting >> "%TEMP%\dctech-watchdog.log"
    wscript "C:\Users\ALI.A.SALAH\Desktop\projects\Front-End Electronics E-commerce Website me\start-frontend-hidden.vbs" >nul 2>&1
)
