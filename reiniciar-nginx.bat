@echo off
title REINICIAR NGINX
net stop Nginx
timeout /t 3 /nobreak >nul
net start Nginx
echo.
echo Nginx reiniciado con la nueva configuracion.
echo.
echo Probando acceso local...
timeout /t 2 /nobreak >nul
curl http://localhost -s -o nul -w "HTTP Status: %%{http_code}"
echo.
pause
