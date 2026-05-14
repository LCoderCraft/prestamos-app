<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: #f3f4f6; padding: 20px; }
        .card { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { color: #4338ca; text-align: center; }
        .status { font-weight: bold; font-size: 1.1em; margin: 10px 0; }
        .approved { color: #16a34a; }
        .rejected { color: #dc2626; }
        .comment { background: #eef2ff; padding: 10px; border-left: 4px solid #4338ca; margin: 10px 0; }
        .footer { margin-top: 20px; text-align: center; font-size: 0.8em; color: #6b7280; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Hola, {{ $reservation->user->username }}</h2>
        
        <p>El estado de tu reservacion para el centro <strong>{{ $reservation->computerRoom->name }}</strong> ha cambiado.</p>

        <p class="status">
            Estado: 
            @if($reservation->status == 'active')
                <span class="approved">APROBADA</span>
            @else
                <span class="rejected">RECHAZADA</span>
            @endif
        </p>

        <p><strong>Fecha:</strong> {{ $reservation->start_date->format('d/m/Y') }}</p>
        <p><strong>Horario:</strong> {{ $reservation->start_date->format('H:i') }} - {{ $reservation->end_date->format('H:i') }}</p>
        <p><strong>Motivo:</strong> {{ $reservation->purpose }}</p>

        @if($reservation->admin_comment)
        <div class="comment">
            <strong>Observacion:</strong><br>
            {{ $reservation->admin_comment }}
        </div>
        @endif

        <div class="footer">
            Sistema de Prestamos UAS<br>
            No respondas a este mensaje automatico.
        </div>
    </div>
</body>
</html>