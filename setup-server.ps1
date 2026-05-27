Write-Host "=== CONFIGURACION DEL SERVIDOR LARAVEL ===" -ForegroundColor Green

# 1. Instalar PHP-CGI como servicio
Write-Host "`n[1/5] Instalando PHP-CGI como servicio..." -ForegroundColor Cyan
& "C:\nginx\nssm.exe" install PHP-CGI "C:\xampp\php\php-cgi.exe" "-b 127.0.0.1:9000" 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set PHP-CGI AppDirectory "C:\xampp\php" 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set PHP-CGI Start SERVICE_AUTO_START 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set PHP-CGI ObjectName LocalSystem 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set PHP-CGI DisplayName "PHP FastCGI (prestamos-app)" 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set PHP-CGI Description "Servicio PHP-CGI para Laravel prestamos-app" 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set PHP-CGI AppStdout "C:\nginx\logs\php-cgi.log" 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set PHP-CGI AppStderr "C:\nginx\logs\php-cgi-error.log" 2>&1 | Out-Null
Write-Host "  Servicio PHP-CGI creado." -ForegroundColor Green

# 2. Instalar Nginx como servicio
Write-Host "`n[2/5] Instalando Nginx como servicio..." -ForegroundColor Cyan
& "C:\nginx\nssm.exe" install Nginx "C:\nginx\nginx.exe" 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set Nginx AppDirectory "C:\nginx" 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set Nginx Start SERVICE_AUTO_START 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set Nginx ObjectName LocalSystem 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set Nginx DisplayName "Nginx Web Server (prestamos-app)" 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set Nginx AppStdout "C:\nginx\logs\nginx.log" 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set Nginx AppStderr "C:\nginx\logs\nginx-error.log" 2>&1 | Out-Null
& "C:\nginx\nssm.exe" set Nginx AppStopMethodConsole 2>&1 | Out-Null
Write-Host "  Servicio Nginx creado." -ForegroundColor Green

# 3. Crear carpeta para logs de Laravel
Write-Host "`n[3/5] Creando directorios necesarios..." -ForegroundColor Cyan
New-Item -ItemType Directory -Path "C:\nginx\logs" -Force | Out-Null
New-Item -ItemType Directory -Path "C:\nginx\conf\sites" -Force | Out-Null
Write-Host "  Directorios creados." -ForegroundColor Green

# 4. Abrir puertos en firewall
Write-Host "`n[4/5] Abriendo puertos en firewall..." -ForegroundColor Cyan
New-NetFirewallRule -DisplayName "Nginx HTTP (80)" -Direction Inbound -Protocol TCP -LocalPort 80 -Action Allow -ErrorAction SilentlyContinue | Out-Null
New-NetFirewallRule -DisplayName "Nginx HTTPS (443)" -Direction Inbound -Protocol TCP -LocalPort 443 -Action Allow -ErrorAction SilentlyContinue | Out-Null
Write-Host "  Puertos 80 y 443 abiertos." -ForegroundColor Green

# 5. Preguntar por DuckDNS
Write-Host "`n[5/5] Configuracion DuckDNS" -ForegroundColor Cyan
$duckdnsDomain = Read-Host "`nIngresa tu dominio DuckDNS (ej: miprestamo.duckdns.org) [deja vacio si no tienes aun]"
if ($duckdnsDomain) {
    $duckdnsToken = Read-Host "Ingresa tu token DuckDNS"
    # Crear script de actualizacion DuckDNS
    $updateScript = @"
`$ip = (Invoke-WebRequest -Uri "https://api.ipify.org" -UseBasicParsing).Content
Invoke-WebRequest -Uri "https://www.duckdns.org/update?domains=$($duckdnsDomain -replace '.duckdns.org','')&token=$duckdnsToken&ip=`$ip" -UseBasicParsing | Out-Null
"@
    Set-Content -Path "C:\nginx\duckdns-update.ps1" -Value $updateScript
    Write-Host "  Script de actualizacion DuckDNS creado." -ForegroundColor Green
    
    # Crear tarea programada para ejecutar cada 5 minutos
    $action = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-NoProfile -WindowStyle Hidden -File C:\nginx\duckdns-update.ps1"
    $trigger = New-ScheduledTaskTrigger -RepetitionInterval (New-TimeSpan -Minutes 5) -At (Get-Date) -Once
    $principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest
    Register-ScheduledTask -TaskName "DuckDNS Update" -Action $action -Trigger $trigger -Principal $principal -Force | Out-Null
    Write-Host "  Tarea programada DuckDNS creada (cada 5 minutos)." -ForegroundColor Green
    
    # Actualizar APP_URL en .env
    $envFile = "C:\prestamos-app\.env"
    $envContent = Get-Content $envFile -Raw
    $envContent = $envContent -replace 'APP_URL=.*', "APP_URL=https://$duckdnsDomain"
    Set-Content -Path $envFile -Value $envContent -NoNewline
    Write-Host "  APP_URL actualizado en .env" -ForegroundColor Green
}

Write-Host "`n=== CONFIGURACION COMPLETADA ===" -ForegroundColor Green
Write-Host "`nAhora puedes iniciar los servicios con:" -ForegroundColor Yellow
Write-Host "  Start-Service PHP-CGI" -ForegroundColor White
Write-Host "  Start-Service Nginx" -ForegroundColor White
Write-Host "`nO desde ventana administrativa:" -ForegroundColor Yellow
Write-Host "  net start PHP-CGI" -ForegroundColor White
Write-Host "  net start Nginx" -ForegroundColor White
