@echo off
rem DCTech Frontend — Vite dev server on port 5173. Safe to run many times:
rem if 5173 is already listening, this exits without doing anything.
netstat -ano | findstr /r /c:"TCP.*:5173 .*LISTENING" >nul
if errorlevel 1 (
    cd /d "C:\Users\ALI.A.SALAH\Desktop\projects\Front-End Electronics E-commerce Website me\frontend"
    if errorlevel 1 ( echo ==== cd FAILED %DATE% %TIME% ==== >> "%TEMP%\dctech-frontend.log" & exit /b 1 )
    echo ==== start %DATE% %TIME% ==== >> "%TEMP%\dctech-frontend.log"
    npm run dev -- --host >> "%TEMP%\dctech-frontend.log" 2>&1
    echo ==== exited with %ERRORLEVEL% %DATE% %TIME% ==== >> "%TEMP%\dctech-frontend.log"
) else (
    echo ==== already running %DATE% %TIME% ==== >> "%TEMP%\dctech-frontend.log"
)
