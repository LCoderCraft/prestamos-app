param(
    [string]$Domain,
    [string]$Token
)

$ErrorActionPreference = "Stop"

function Write-Step {
    param($Msg)
    Write-Host "`n>>> $Msg" -ForegroundColor Cyan
}

function Write-OK {
    Write-Host "  [OK] $($args[0])" -ForegroundColor Green
}

function Write-Warn {
    Write-Host "  [!] $($args[0])" -ForegroundColor Yellow
}

# ---- Solicitar datos si no se pasaron como parámetros ----
if (-not $Domain) {
    $Domain = Read-Host "`nIngresa tu dominio DuckDNS (ej: miprestamo.duckdns.org)"
}
if (-not $Token) {
    $Token = Read-Host "Ingresa tu token DuckDNS (copia de duckdns.org)"
}

$Subdomain = $Domain -replace '\.duckdns\.org$', ''
if (-not $Subdomain) {
    Write-Host "ERROR: Dominio invalido. Debe ser algo como tudominio.duckdns.org" -ForegroundColor Red
    exit 1
}

Write-Host "`n========================================" -ForegroundColor Green
Write-Host "CONFIGURANDO DOMINIO: $Domain" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Green

# ============================================================
# 1. CREAR SCRIPT DE ACTUALIZACION DUCKDNS
# ============================================================
Write-Step "[1/6] Creando script de actualizacion DuckDNS"

$updateScript = @"
`$ip = (Invoke-WebRequest -Uri "https://api.ipify.org" -UseBasicParsing).Content
Invoke-WebRequest -Uri "https://www.duckdns.org/update?domains=$Subdomain&token=$Token&ip=`$ip" -UseBasicParsing | Out-Null
"@

Set-Content -Path "C:\nginx\duckdns-update.ps1" -Value $updateScript -Force
Write-OK "Script creado: C:\nginx\duckdns-update.ps1"

# ============================================================
# 2. CREAR TAREA PROGRAMADA
# ============================================================
Write-Step "[2/6] Creando tarea programada (cada 5 minutos)"

try {
    $action = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-NoProfile -WindowStyle Hidden -File C:\nginx\duckdns-update.ps1"
    $trigger = New-ScheduledTaskTrigger -RepetitionInterval (New-TimeSpan -Minutes 5) -At (Get-Date).AddMinutes(1) -Once
    $principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest
    Register-ScheduledTask -TaskName "DuckDNS_Update_$Subdomain" -Action $action -Trigger $trigger -Principal $principal -Force | Out-Null
    Write-OK "Tarea programada creada: DuckDNS_Update_$Subdomain"
} catch {
    Write-Warn "No se pudo crear la tarea programada (se necesita Admin)."
    Write-Warn "Ejecuta este script como Administrador."
}

# ============================================================
# 3. ACTUALIZAR APP_URL EN .env
# ============================================================
Write-Step "[3/6] Actualizando APP_URL en .env"

$envFile = "C:\prestamos-app\.env"
$envContent = Get-Content $envFile -Raw
$envContent = $envContent -replace 'APP_URL=.*', "APP_URL=https://$Domain"
Set-Content -Path $envFile -Value $envContent -NoNewline
Write-OK "APP_URL actualizado a https://$Domain"

# ============================================================
# 4. ACTUALIZAR CONFIGURACION DE NGINX
# ============================================================
Write-Step "[4/6] Actualizando configuracion Nginx con el dominio"

$nginxConf = Get-Content "C:\nginx\conf\sites\prestamos-app.conf" -Raw
$nginxConf = $nginxConf -replace 'server_name _;', "server_name $Domain;"
Set-Content -Path "C:\nginx\conf\sites\prestamos-app.conf" -Value $nginxConf -Force
Write-OK "Nginx configurado con server_name $Domain"

# ============================================================
# 5. ACTIVAR HTTPS EN NGINX
# ============================================================
Write-Step "[5/6] Preparando configuracion HTTPS"

# Check if SSL certs exist
$certPath = "C:\nginx\ssl"
$certFile = "$certPath\cert.pem"
$keyFile = "$certPath\key.pem"

if ((Test-Path $certFile) -and (Test-Path $keyFile)) {
    Write-OK "Certificados SSL encontrados, activando HTTPS..."
    
    $httpsConfig = @'

server {
    listen 443 ssl http2;
    server_name DOMAIN_PLACEHOLDER;

    ssl_certificate     C:/nginx/ssl/cert.pem;
    ssl_certificate_key C:/nginx/ssl/key.pem;
    ssl_protocols       TLSv1.2 TLSv1.3;
    ssl_ciphers         HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache   shared:SSL:10m;
    ssl_session_timeout 10m;

    root C:/prestamos-app/public;
    index index.php;

    access_log logs/prestamos-ssl-access.log;
    error_log  logs/prestamos-ssl-error.log;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" preload;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
        fastcgi_param  PHP_VALUE "upload_max_filesize = 20M \n post_max_size = 20M";
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
    }

    location ~ /\.ht {
        deny all;
    }
}
'@
    $httpsConfig = $httpsConfig -replace 'DOMAIN_PLACEHOLDER', $Domain
    Add-Content -Path "C:\nginx\conf\sites\prestamos-app.conf" -Value "`n$httpsConfig"
    Write-OK "HTTPS activado en Nginx"
} else {
    Write-Warn "No se encontraron certificados SSL en $certPath"
    Write-Warn "Para generar certificados SSL, necesitaras configurar SSL manualmente."
    Write-Warn "Te recomiendo usar Win-ACME (https://www.win-acme.com/)"
    
    # Crear directorio SSL
    New-Item -ItemType Directory -Path $certPath -Force | Out-Null
}

# ============================================================
# 6. PROBAR CONFIGURACION NGINX
# ============================================================
Write-Step "[6/6] Probando configuracion Nginx"

C:\nginx\nginx.exe -t 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-OK "Configuracion Nginx VALIDA"
} else {
    Write-Warn "La configuracion de Nginx tiene errores, revisa el mensaje arriba"
}

Write-Host "`n========================================" -ForegroundColor Green
Write-Host "CONFIGURACION COMPLETADA" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host "`nResumen:" -ForegroundColor Yellow
Write-Host "  Dominio:     $Domain" -ForegroundColor White
Write-Host "  APP_URL:     https://$Domain" -ForegroundColor White
Write-Host "  IP publica:  $(Invoke-WebRequest -Uri "https://api.ipify.org" -UseBasicParsing).Content" -ForegroundColor White
Write-Host "`nPasos siguientes:"
Write-Host "  1. Ejecuta como ADMIN: C:\prestamos-app\setup-server.ps1" -ForegroundColor White
Write-Host "  2. Abre puertos 80 y 443 en tu router (reenvio a 192.168.31.99)" -ForegroundColor White
Write-Host "  3. Inicia servicios:" -ForegroundColor White
Write-Host "     net start PHP-CGI" -ForegroundColor White
Write-Host "     net start Nginx" -ForegroundColor White
Write-Host "`nPara renovar certificados SSL automaticamente, usa Win-ACME (https://www.win-acme.com/)" -ForegroundColor Yellow
