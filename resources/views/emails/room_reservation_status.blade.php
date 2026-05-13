<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; background: #f4f4f5; padding: 20px; }
        .card { background: white; border-radius: 12px; padding: 24px; max-width: 500px; margin: auto; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-active { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="margin-top:0;color:#1e1b4b;">
            {{ $reservation->status === 'active' ? '✅ Reservación Aprobada' : '❌ Reservación Rechazada' }}
        </h2>
        <p><strong>Centro:</strong> {{ $reservation->computerRoom->name }}</p>
        <p><strong>Fecha:</strong> {{ $reservation->start_date->format('d/m/Y') }}</p>
        <p><strong>Horario:</strong> {{ $reservation->start_date->format('H:i') }} - {{ $reservation->end_date->format('H:i') }}</p>
        <p><strong>Motivo:</strong> {{ $reservation->purpose }}</p>
        @if($reservation->admin_comment)
            <p><strong>Observación:</strong> {{ $reservation->admin_comment }}</p>
        @endif
        <p style="margin-top:20px;font-size:12px;color:#6b7280;">Facultad de Ingeniería Mochis - Sistema de Préstamos</p>
    </div>
</body>
</html>