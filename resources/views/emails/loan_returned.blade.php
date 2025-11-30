<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: #f3f4f6; padding: 20px; }
        .card { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; border-top: 5px solid #4f46e5; }
        h2 { color: #333; }
        .info { margin: 15px 0; padding: 10px; background: #f9fafb; border-radius: 5px; }
        .observation { margin-top: 20px; padding: 15px; border: 1px solid #e5e7eb; border-left: 4px solid #f59e0b; background: #fffbeb; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Hola, {{ $loan->user->username }}</h2>
        <p>Hemos recibido el material <strong>{{ $loan->item->name }}</strong>.</p>
        
        <div class="info">
            <p><strong>Fecha de Devolución:</strong> {{ now()->format('d/m/Y H:i') }}</p>
            <p><strong>Recibido por:</strong> Administrador</p>
        </div>

        <div class="observation">
            <strong>Observaciones de entrega:</strong><br>
            {{ $observation }}
        </div>

        <p style="font-size: 0.8em; color: #666; margin-top: 30px;">
            Este correo sirve como comprobante de que has entregado el material.
        </p>
    </div>
</body>
</html>