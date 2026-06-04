# Sistema de Control de Prestamos - FIM UAS

Sistema web para la gestion de prestamos de equipo y reservacion de centros de computo de la **Facultad de Ingenieria Mochis (FIM)** de la **Universidad Autonoma de Sinaloa (UAS)**.

**Produccion:** https://prestamos-app.onrender.com

---

## Funcionalidades

- **Prestamo de equipos** — Solicitud, aprobacion/rechazo y devolucion de materiales (laptops, herramientas, etc.) con control de inventario.
- **Reserva de centros de computo** — Calendario semanal (lunes a viernes, 7am-8pm) con reservas por usuario, grupo o maestro.
- **Generacion de codigos de barras** — Formato `UAS-INV-XXXXXX` con impresion y regeneracion via JsBarcode.
- **Reportes PDF** — Reportes diarios, semanales y mensuales con estadisticas de prestamos y reservas (DomPDF).
- **Chat de soporte** — Sistema de mensajeria con lectura de confirmacion, exportacion a texto plano y polling en tiempo real.
- **Notificaciones** — Notificaciones en base de datos con polling cada 4 segundos y sonido.
- **Restablecimiento de contrasena por codigo** — Codigo numerico de 6 digitos enviado por correo en lugar del enlace tradicional.
- **Accesibilidad** — Modo oscuro, alto contraste, ajuste de tamano de fuente (80-150%) e indicadores visuales.

---

## Tecnologias

| Capa | Tecnologia |
|------|-----------|
| Backend | Laravel 12, PHP 8.2 |
| Frontend | Blade, Tailwind CSS, Alpine.js, Vite |
| Base de datos | PostgreSQL (produccion), MySQL (local) |
| PDF | barryvdh/laravel-dompdf |
| Codigos de barras | JsBarcode (CDN) |
| Despliegue | Render.com (plan gratuito) |
| Correo | SMTP via Gmail |

---

## Instalacion local

```bash
git clone https://github.com/tu-usuario/prestamos-app.git
cd prestamos-app
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### Desarrollo

```bash
npm run dev    # Vite en modo desarrollo
php artisan serve
php artisan queue:listen --tries=1
```

---

## Despliegue en produccion (Windows con Nginx)

1. Configura un dominio en [DuckDNS](https://duckdns.org).
2. Ejecuta como Administrador:
   ```powershell
   .\configurar-dominio.ps1    # Configura DuckDNS y Nginx
   .\setup-server.ps1          # Instala PHP-CGI y Nginx como servicios
   ```
3. Inicia servicios: `net start PHP-CGI` y `net start Nginx`.
4. Abre puertos 80 y 443 en tu router.
5. Configura SSL con [Win-ACME](https://www.win-acme.com/).

Ver `GUIASERVIDOR.md` para la guia detallada.

---

## Estructura del proyecto

### Base de datos — 13 tablas principales

| Tabla | Descripcion |
|-------|-------------|
| `users` | Usuarios con roles `user` o `admin` |
| `items` | Inventario de equipos prestables |
| `loans` | Solicitudes de prestamo de equipo |
| `computer_rooms` | Centros de computo registrados |
| `room_reservations` | Reservas de centros de computo |
| `support_chats` | Hilos de chat de soporte |
| `chat_messages` | Mensajes individuales del chat |

### Rutas principales

| Ruta | Metodo | Acceso | Descripcion |
|------|--------|--------|-------------|
| `/dashboard` | GET | Usuario | Panel principal: equipos, prestamos activos, historial |
| `/loans` | POST | Usuario | Crear solicitud de prestamo |
| `/rooms` | GET | Usuario | Calendario semanal de reservas |
| `/rooms/reserve` | POST | Usuario | Reservar centro de computo |
| `/support/chat` | GET/POST | Usuario | Gestionar chat de soporte |
| `/admin` | GET | Admin | Panel de administracion |
| `/admin/reportes` | GET | Admin | Reportes PDF |
| `/admin/codigos` | GET | Admin | Gestion de codigos de barras |
| `/admin/rooms` | GET | Admin | Gestion de centros de computo |

### Modelos y relaciones clave

```
User -- hasMany --> Loan
User -- hasMany --> RoomReservation
User -- hasMany --> SupportChat
Item -- hasMany --> Loan
ComputerRoom -- hasMany --> RoomReservation
SupportChat -- hasMany --> ChatMessage
```

Cada modelo de `Item` y `ComputerRoom` implementa `isAvailable($start, $end)` para validar disponibilidad por rango de fechas.

---

## Roles de usuario

- **user** — Puede solicitar prestamos, reservar centros de computo, usar el chat de soporte y ver su historial.
- **admin** — Puede aprobar/rechazar prestamos y reservas, gestionar inventario y centros de computo, generar reportes y codigos de barras, y responder chats.

---

## Licencia

MIT
