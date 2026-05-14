<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1f2937; padding: 20px; }
        h1 { font-size: 18px; color: #4338ca; border-bottom: 2px solid #4338ca; padding-bottom: 8px; }
        h2 { font-size: 14px; color: #374151; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #4338ca; color: white; padding: 8px 10px; text-align: left; font-size: 11px; }
        td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) { background: #f9fafb; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-active { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-finished { background: #e5e7eb; color: #374151; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 30px; font-size: 10px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .summary { display: flex; gap: 20px; margin-top: 15px; }
        .summary-box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 16px; flex: 1; text-align: center; }
        .summary-box h3 { font-size: 11px; color: #6b7280; margin: 0 0 5px; }
        .summary-box .num { font-size: 22px; font-weight: bold; color: #4338ca; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p style="color:#6b7280;margin-top:4px;">Facultad de Ingeniería Mochis - Sistema de Control de Préstamos</p>

    <div class="summary">
        <div class="summary-box">
            <h3>Préstamos de Equipos</h3>
            <div class="num">{{ $loans->count() }}</div>
        </div>
        <div class="summary-box">
            <h3>Reservaciones de Centros</h3>
            <div class="num">{{ $reservations->count() }}</div>
        </div>
        <div class="summary-box">
            <h3>Total Movimientos</h3>
            <div class="num">{{ $loans->count() + $reservations->count() }}</div>
        </div>
    </div>

    <h2>Préstamos de Equipos</h2>
    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Equipo</th>
                <th>Fecha</th>
                <th>Horario</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $loan)
            <tr>
                <td>{{ $loan->user->username }}</td>
                <td>{{ $loan->item->name }}</td>
                <td>{{ $loan->start_date->format('d/m/Y') }}</td>
                <td>{{ $loan->start_date->format('H:i') }} - {{ $loan->end_date->format('H:i') }}</td>
                <td><span class="badge badge-{{ $loan->status }}">{{ ucfirst($loan->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#9ca3af;">Sin préstamos en este período</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Reservaciones de Centros de Cómputo</h2>
    <table>
        <thead>
            <tr>
                <th>Solicitante</th>
                <th>Centro</th>
                <th>Fecha</th>
                <th>Horario</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservations as $res)
            <tr>
                <td>{{ $res->requester_type === 'group' ? 'Grupo '.$res->group_name : ($res->requester_type === 'teacher' ? $res->teacher_name : $res->user->username) }}</td>
                <td>{{ $res->computerRoom->name }}</td>
                <td>{{ $res->start_date->format('d/m/Y') }}</td>
                <td>{{ $res->start_date->format('H:i') }} - {{ $res->end_date->format('H:i') }}</td>
                <td><span class="badge badge-{{ $res->status }}">{{ ucfirst($res->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#9ca3af;">Sin reservaciones en este período</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} - Facultad de Ingeniería Mochis
    </div>
</body>
</html>
