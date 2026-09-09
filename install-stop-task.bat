@echo off
schtasks /create /tn "DCTech Stop Shutdown" /tr "\"C:\Users\ALI.A.SALAH\Desktop\Front-End Electronics E-commerce Website me\outstop.cmd\"" /sc onshutdown /rl highest /f
echo Shutdown task created!
pause
