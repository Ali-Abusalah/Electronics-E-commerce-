@echo off
echo Opening firewall ports for DCTech Shop...
netsh advfirewall firewall add rule name="DCTech Backend 8000" dir=in action=allow protocol=TCP localport=8000
netsh advfirewall firewall add rule name="DCTech Frontend 5173" dir=in action=allow protocol=TCP localport=5173
echo.
echo Done! Ports 8000 and 5173 are now open.
echo.
pause
