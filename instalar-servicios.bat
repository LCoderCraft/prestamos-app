@echo off
title INSTALACION SERVIDOR PRESTAMOS-APP
cd /d "C:\prestamos-app"

echo ========================================
echo  Instalando servicios del servidor
echo ========================================
echo.

:: 1. Instalar PHP-CGI como servicio
echo [1/4] Instalando PHP-CGI como servicio...
C:\nginx\nssm.exe install PHP-CGI "C:\xampp\php\php-cgi.exe" "-b 127.0.0.1:9000" >nul
C:\nginx\nssm.exe set PHP-CGI AppDirectory "C:\xampp\php" >nul
C:\nginx\nssm.exe set PHP-CGI Start SERVICE_AUTO_START >nul
C:\nginx\nssm.exe set PHP-CGI ObjectName LocalSystem >nul
C:\nginx\nssm.exe set PHP-CGI DisplayName "PHP FastCGI (prestamos-app)" >nul
C:\nginx\nssm.exe set PHP-CGI Description "Servicio PHP-CGI para Laravel prestamos-app" >nul
C:\nginx\nssm.exe set PHP-CGI AppStdout "C:\nginx\logs\php-cgi.log" >nul
C:\nginx\nssm.exe set PHP-CGI AppStderr "C:\nginx\logs\php-cgi-error.log" >nul
echo  [OK] PHP-CGI instalado

:: 2. Instalar Nginx como servicio
echo [2/4] Instalando Nginx como servicio...
C:\nginx\nssm.exe install Nginx "C:\nginx\nginx.exe" >nul
C:\nginx\nssm.exe set Nginx AppDirectory "C:\nginx" >nul
C:\nginx\nssm.exe set Nginx Start SERVICE_AUTO_START >nul
C:\nginx\nssm.exe set Nginx ObjectName LocalSystem >nul
C:\nginx\nssm.exe set Nginx DisplayName "Nginx Web Server (prestamos-app)" >nul
C:\nginx\nssm.exe set Nginx AppStdout "C:\nginx\logs\nginx.log" >nul
C:\nginx\nssm.exe set Nginx AppStderr "C:\nginx\logs\nginx-error.log" >nul
echo  [OK] Nginx instalado

:: 3. Crear tarea programada DuckDNS
echo [3/4] Creando tarea programada DuckDNS...
schtasks /create /tn "DuckDNS_Update_tarea-prestamos" /tr "powershell.exe -NoProfile -WindowStyle Hidden -File C:\nginx\duckdns-update.ps1" /sc minute /mo 5 /ru SYSTEM /f >nul 2>&1
if %errorlevel% equ 0 (
    echo  [OK] Tarea DuckDNS creada (cada 5 min)
) else (
    echo  [WARN] No se pudo crear la tarea - continua de todos modos
)

:: 4. Abrir puertos en firewall
echo [4/4] Abriendo puertos en firewall...
netsh advfirewall firewall add rule name="Nginx HTTP (80)" dir=in action=allow protocol=TCP localport=80 >nul 2>&1
netsh advfirewall firewall add rule name="Nginx HTTPS (443)" dir=in action=allow protocol=TCP localport=443 >nul 2>&1
echo  [OK] Puertos 80 y 443 abiertos

echo.
echo ========================================
echo  INSTALACION COMPLETADA
echo ========================================
echo.
echo Ahora inicia los servicios:
echo   net start PHP-CGI
echo   net start Nginx
echo.
echo Para verificar: curl http://localhost
echo.
pause
