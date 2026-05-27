# GUIA DE PUESTA EN PRODUCCION - PRESTAMOS APP

## Requisitos previos
- PHP 8.2 (instalado en C:\xampp\php)
- MySQL (en XAMPP)
- Nginx (instalado en C:\nginx)
- Dominio DuckDNS creado

---

## PASO 1: Configurar DuckDNS

1. Ve a https://duckdns.org e inicia sesion
2. Crea un dominio (ej: `miprestamo.duckdns.org`)
3. Copia tu **token**

Luego ejecuta (como Administrador):
```powershell
cd C:\prestamos-app
.\configurar-dominio.ps1
```
Te pedira el dominio y el token.

---

## PASO 2: Ejecutar script de servicios

Como **Administrador**, ejecuta:
```powershell
cd C:\prestamos-app
.\setup-server.ps1
```

Esto instalara PHP-CGI y Nginx como servicios de Windows, y abrira los puertos 80 y 443 en el firewall.

---

## PASO 3: Iniciar servicios

```powershell
net start PHP-CGI
net start Nginx
```

Verificar que funcionan:
```powershell
curl http://localhost
```

---

## PASO 4: Abrir puertos en el router

Entra a la configuracion de tu router (http://192.168.31.1 o http://192.168.0.1)
y agrega **reenvio de puertos (port forwarding)**:

| Puerto externo | Puerto interno | IP destino    |
|---------------|----------------|---------------|
| 80            | 80             | 192.168.31.99 |
| 443           | 443            | 192.168.31.99 |

Si tu router lo pide, selecciona protocolo **TCP**.

---

## PASO 5: SSL con Let's Encrypt (HTTPS)

Descarga **Win-ACME** desde: https://www.win-acme.com/

```powershell
# Descargar wacs.exe a C:\nginx\
# Luego ejecutar:
cd C:\nginx
.\wacs.exe
```

En el menu interactivo:
1. Selecciona `M` (Create certificate with full options)
2. Introduce tu dominio: `miprestamo.duckdns.org` (el tuyo)
3. Selecciona `1` ([http-01] Verify via http)
4. Acepta las opciones por defecto
5. Al final, da la ruta para los certificados: `C:\nginx\ssl\`
6. Nombra el certificado: `cert`

Al terminar, vuelve a ejecutar el script de configuracion:
```powershell
cd C:\prestamos-app
.\configurar-dominio.ps1
```
Detectara los certificados y activara HTTPS automaticamente.

---

## PASO 6: Verificar acceso externo

Desde cualquier dispositivo fuera de tu red visita:
```
https://tudominio.duckdns.org
```

---

## Mantenimiento

### Renovar SSL
Win-ACME crea una tarea programada que renueva automaticamente.

### Actualizar IP publica
DuckDNS se actualiza automaticamente cada 5 minutos via tarea programada.

### Detener servicios
```powershell
net stop Nginx
net stop PHP-CGI
```

### Ver logs de error
```powershell
Get-Content C:\nginx\logs\prestamos-error.log
```

---

## Solucion de problemas

**Error: "Connection refused"**
- Verifica que PHP-CGI y Nginx esten corriendo: `Get-Service PHP-CGI, Nginx`
- Revisa logs: `Get-Content C:\nginx\logs\error.log`

**Error: "No se puede acceder desde internet"**
- Verifica IP publica: visita https://api.ipify.org
- Verifica que tu IP local sea: 192.168.31.99 (ejecuta `ipconfig`)
- Revisa port forwarding en el router
- Revisa firewall de Windows

**Error de PHP**
```powershell
Get-Content C:\nginx\logs\php-cgi-error.log
```
