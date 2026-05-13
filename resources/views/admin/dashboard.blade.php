<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Préstamos FIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 min-h-screen font-sans">

    <nav class="bg-indigo-800 shadow-lg border-b-4 border-indigo-500">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-white text-indigo-800 p-2 rounded-lg shadow-sm">
                    <i class="fa-solid fa-projector text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-wide">Administración FIM</h1>
                    <p class="text-xs text-indigo-200">Sistema de Control de Préstamos</p>
                </div>
            </div>
            <div class="flex items-center space-x-4 bg-indigo-900 px-4 py-2 rounded-full border border-indigo-700 shadow-inner">
                <i class="fa-solid fa-user-shield text-indigo-300"></i>
                <span class="text-sm font-medium text-indigo-100 border-r border-indigo-600 pr-4">Hola, Admin</span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="text-sm text-red-300 hover:text-red-100 font-semibold transition-colors flex items-center gap-2">
                        <span>Salir</span> <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    @if(auth()->user()->unreadNotifications->count() > 0)
    <div class="max-w-7xl mx-auto mt-6 px-4"> 
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-md flex justify-between items-start">
            <div class="flex gap-3">
                <i class="fa-solid fa-bell text-amber-500 text-xl mt-1 animate-bounce"></i>
                <div>
                    <p class="font-bold text-amber-800">Tienes nuevas solicitudes de equipo:</p>
                    <ul class="mt-2 space-y-1">
                        @foreach(auth()->user()->unreadNotifications as $notification)
                            <li class="text-sm text-amber-700 flex items-center bg-amber-100 px-3 py-1 rounded-md w-fit">
                                <i class="fa-solid fa-caret-right mr-2 text-amber-500"></i>
                                {{ $notification->data['message'] }} <strong class="ml-1">({{ $notification->data['item'] }})</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <a href="{{ route('notifications.read') }}" class="text-xs bg-amber-500 text-white px-3 py-2 rounded shadow hover:bg-amber-600 transition flex items-center gap-1 font-bold">
                <i class="fa-solid fa-check-double"></i> Marcar leídas
            </a>
        </div>
    </div>
    @endif

    <div class="max-w-7xl mx-auto p-4 mt-2">
        
        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-lg mb-6 shadow-sm border border-emerald-200 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-lg"><i class="fa-solid fa-box-open text-xl"></i></div>
                <div><p class="text-sm text-gray-500 font-semibold">Préstamos Activos</p><p class="text-2xl font-bold text-gray-800">{{ $activeLoans->where('status', 'active')->count() ?? '0' }}</p></div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="bg-amber-100 text-amber-600 p-3 rounded-lg"><i class="fa-solid fa-clock-rotate-left text-xl"></i></div>
                <div><p class="text-sm text-gray-500 font-semibold">Solicitudes Pendientes</p><p class="text-2xl font-bold text-gray-800">{{ $activeLoans->where('status', 'pending')->count() ?? '0' }}</p></div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="bg-indigo-100 text-indigo-600 p-3 rounded-lg"><i class="fa-solid fa-boxes-stacked text-xl"></i></div>
                <div><p class="text-sm text-gray-500 font-semibold">Equipos en Inventario</p><p class="text-2xl font-bold text-gray-800">Gestionar ➔</p></div>
            </div>
        </div>

        <div class="mb-6 bg-white rounded-t-lg shadow-sm px-2 pt-2 border-b-2 border-gray-200">
            <nav class="flex space-x-2 overflow-x-auto">
                <button onclick="switchTab('loans')" id="tab-loans" class="whitespace-nowrap py-3 px-4 rounded-t-lg font-bold text-sm text-indigo-700 bg-indigo-50 border-b-2 border-indigo-600 transition-colors">
                    <i class="fa-solid fa-hand-holding-hand mr-2"></i>Gestión de Préstamos
                </button>
                <button onclick="switchTab('history')" id="tab-history" class="whitespace-nowrap py-3 px-4 rounded-t-lg font-medium text-sm text-gray-500 hover:text-indigo-600 hover:bg-gray-50 transition-colors border-b-2 border-transparent">
                    <i class="fa-solid fa-clock-rotate-left mr-2"></i>Historial
                </button>
                <button onclick="switchTab('inventory')" id="tab-inventory" class="whitespace-nowrap py-3 px-4 rounded-t-lg font-medium text-sm text-gray-500 hover:text-indigo-600 hover:bg-gray-50 transition-colors border-b-2 border-transparent">
                    <i class="fa-solid fa-boxes-stacked mr-2"></i>Inventario
                </button>
                <button onclick="switchTab('reportes')" id="tab-reportes" class="whitespace-nowrap py-3 px-4 rounded-t-lg font-medium text-sm text-gray-500 hover:text-indigo-600 hover:bg-gray-50 transition-colors border-b-2 border-transparent">
                    <i class="fa-solid fa-chart-pie mr-2"></i>Reportes
                </button>
                <button onclick="switchTab('codigos')" id="tab-codigos" class="whitespace-nowrap py-3 px-4 rounded-t-lg font-medium text-sm text-gray-500 hover:text-indigo-600 hover:bg-gray-50 transition-colors border-b-2 border-transparent">
                    <i class="fa-solid fa-barcode mr-2"></i>Códigos de Barras
                </button>
            </nav>
        </div>

        <div class="bg-white p-6 rounded-b-lg rounded-tr-lg shadow-xl w-full border border-gray-100">
            
            <div id="content-loans" class="animate-fadeIn">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800"><i class="fa-solid fa-list-check text-indigo-600 mr-2"></i>Solicitudes Pendientes y Activas</h2>
                </div>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Equipo / Producto</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Solicitante</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Horario Solicitado</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($activeLoans as $loan)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-indigo-900">{{ $loan->item->name }}</div>
                                    <div class="text-xs text-gray-500">Cod: {{ $loan->item->barcode ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800">{{ $loan->user->username }}</div>
                                    <div class="text-xs text-gray-500"><i class="fa-solid fa-phone mr-1"></i>{{ $loan->user->phone }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-800 font-medium"><i class="fa-regular fa-calendar mr-1"></i>{{ $loan->start_date->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500"><i class="fa-regular fa-clock mr-1"></i>{{ $loan->start_date->format('H:i') }} - {{ $loan->end_date->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $loan->status=='pending'?'bg-amber-100 text-amber-800 border border-amber-200':'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                        @if($loan->status=='pending') <i class="fa-solid fa-hourglass-half mr-1 mt-0.5"></i> Pendiente
                                        @else <i class="fa-solid fa-circle-play mr-1 mt-0.5"></i> En Curso @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium flex justify-center space-x-2">
                                    @if($loan->status == 'pending')
                                        <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
                                            @csrf <input type="hidden" name="action" value="approve">
                                            <button class="bg-emerald-500 text-white px-3 py-2 rounded-md hover:bg-emerald-600 transition shadow-sm" title="Aprobar Solicitud">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                        <button onclick="confirmReject({{ $loan->id }})" class="bg-red-500 text-white px-3 py-2 rounded-md hover:bg-red-600 transition shadow-sm" title="Rechazar Solicitud">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                        <form id="form-reject-{{ $loan->id }}" action="{{ route('admin.loans.update', $loan->id) }}" method="POST" class="hidden">
                                            @csrf <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="comment" id="comment-reject-{{ $loan->id }}">
                                        </form>
                                    @endif
                                    @if($loan->status == 'active')
                                        <button onclick="openReturnModal({{ $loan->id }}, '{{ $loan->user->username }}', '{{ $loan->item->name }}')" 
                                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition shadow-sm flex items-center font-semibold">
                                            <i class="fa-solid fa-box-open mr-2"></i> Recibir Equipo
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <i class="fa-solid fa-mug-hot text-4xl mb-3"></i>
                                        <p class="text-lg font-medium text-gray-500">Todo al día</p>
                                        <p class="text-sm">No hay solicitudes pendientes ni equipos en préstamo actualmente.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="content-history" class="hidden animate-fadeIn">
                <h2 class="text-2xl font-bold text-gray-800 mb-6"><i class="fa-solid fa-clock-rotate-left text-indigo-600 mr-2"></i>Historial Completo</h2>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Fecha Cierre</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Equipo</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Resolución</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($historyLoans as $loan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-600"><i class="fa-regular fa-calendar-check mr-1"></i>{{ $loan->updated_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $loan->item->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $loan->user->username }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-bold rounded-md {{ $loan->status=='finished'?'bg-gray-100 text-gray-700 border border-gray-300':'bg-red-50 text-red-700 border border-red-200' }}">
                                        @if($loan->status == 'finished') <i class="fa-solid fa-check"></i> Finalizado
                                        @else <i class="fa-solid fa-ban"></i> Rechazado @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 italic">
                                    {{ str_replace('DEVOLUCIÓN: ', '', $loan->admin_comment) ?? 'Sin observaciones' }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="p-8 text-center text-gray-400"><i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>El historial está vacío.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="content-inventory" class="hidden animate-fadeIn">
                <h2 class="text-2xl font-bold text-gray-800 mb-6"><i class="fa-solid fa-boxes-stacked text-indigo-600 mr-2"></i>Gestión de Inventario</h2>
                
                <div class="bg-indigo-50/50 p-5 rounded-xl border border-indigo-100 mb-8 shadow-sm">
                    <h3 class="font-bold text-indigo-800 mb-3 text-sm flex items-center"><i class="fa-solid fa-plus-circle mr-2"></i>Registrar Nuevo Equipo</h3>
                    <form action="{{ route('admin.items.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                        @csrf
                        <div class="flex-1 w-full">
                            <label class="text-xs font-bold text-gray-700 mb-1 block">Nombre del Equipo</label>
                            <div class="relative">
                                <i class="fa-solid fa-tag absolute left-3 top-3 text-gray-400"></i>
                                <input type="text" name="name" class="w-full pl-9 p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Ej. Proyector Epson X20" required>
                            </div>
                        </div>
                        <div class="w-32">
                            <label class="text-xs font-bold text-gray-700 mb-1 block">Cantidad</label>
                            <input type="number" name="total_count" min="1" value="1" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none text-center" required>
                        </div>
                        <div class="flex-1 w-full">
                            <label class="text-xs font-bold text-gray-700 mb-1 block">URL de Fotografía (Opcional)</label>
                            <div class="relative">
                                <i class="fa-solid fa-image absolute left-3 top-3 text-gray-400"></i>
                                <input type="url" name="photo_url" class="w-full pl-9 p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="https://...">
                            </div>
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white font-bold py-2.5 px-6 rounded-lg hover:bg-indigo-700 transition shadow-md whitespace-nowrap">
                            Agregar al Catálogo
                        </button>
                    </form>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($items as $item)
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow p-5 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-2">
                            <span class="px-2 py-1 text-[10px] uppercase font-bold rounded-full {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $item->is_active ? 'Disponible' : 'Oculto' }}
                            </span>
                        </div>
                        <div class="flex items-start mb-4 mt-2">
                            <div class="bg-gray-100 p-3 rounded-lg mr-4">
                                @if($item->photo_url)
                                    <img src="{{ $item->photo_url }}" alt="img" class="h-10 w-10 object-cover rounded">
                                @else
                                    <i class="fa-solid fa-video text-2xl text-gray-400"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $item->name }}</h4>
                                <p class="text-sm text-gray-500">Stock total: <span class="font-bold text-indigo-600">{{ $item->total_count }}</span> unid.</p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 flex flex-col items-center justify-center mb-4 min-h-[5rem]">
                            @if($item->barcode)
                                <img src="https://barcode.tec-it.com/barcode.ashx?data={{ $item->barcode }}&code=Code128" class="h-8 object-contain mix-blend-multiply" alt="Código">
                                <p class="text-[10px] font-mono text-gray-500 mt-1">{{ $item->barcode }}</p>
                            @else
                                <span class="text-xs text-gray-400 italic"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Sin código de barras</span>
                            @endif
                        </div>
                        
                        <button onclick="openEditModal({{ $item }})" class="w-full bg-gray-100 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 font-semibold py-2 rounded-lg transition-colors border border-gray-200 text-sm">
                            <i class="fa-solid fa-pen mr-2"></i>Editar Equipo
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div id="content-reportes" class="hidden animate-fadeIn">
                <h2 class="text-2xl font-bold text-gray-800 mb-2"><i class="fa-solid fa-chart-pie text-indigo-600 mr-2"></i>Módulo de Reportes</h2>
                <p class="text-sm text-gray-600 mb-8 border-b pb-4">Genera reportes de la actividad y uso de los equipos en la facultad.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <button class="group bg-white border-2 border-indigo-100 p-6 rounded-xl hover:border-indigo-500 hover:shadow-lg transition-all text-left">
                        <div class="bg-indigo-100 text-indigo-600 w-12 h-12 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-calendar-day text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg mb-1">Corte Diario</h3>
                        <p class="text-xs text-gray-500">Préstamos completados y pendientes de hoy.</p>
                    </button>
                    <button class="group bg-white border-2 border-indigo-100 p-6 rounded-xl hover:border-indigo-500 hover:shadow-lg transition-all text-left">
                        <div class="bg-indigo-100 text-indigo-600 w-12 h-12 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-calendar-week text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg mb-1">Resumen Semanal</h3>
                        <p class="text-xs text-gray-500">Estadísticas de uso de los últimos 7 días.</p>
                    </button>
                    <button class="group bg-white border-2 border-indigo-100 p-6 rounded-xl hover:border-indigo-500 hover:shadow-lg transition-all text-left">
                        <div class="bg-indigo-100 text-indigo-600 w-12 h-12 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-chart-line text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg mb-1">Métricas Mensuales</h3>
                        <p class="text-xs text-gray-500">Equipos más usados y usuarios frecuentes.</p>
                    </button>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 flex flex-col md:flex-row justify-between items-center">
                    <div>
                        <h4 class="font-bold text-gray-800">Exportar Datos</h4>
                        <p class="text-sm text-gray-500">Descarga la base de datos completa de movimientos en formato PDF.</p>
                    </div>
                    <button class="mt-4 md:mt-0 bg-red-600 text-white px-6 py-3 rounded-lg shadow hover:bg-red-700 font-bold text-sm flex items-center transition">
                        <i class="fa-solid fa-file-pdf text-lg mr-2"></i> Generar Documento PDF
                    </button>
                </div>
            </div>

            <div id="content-codigos" class="hidden animate-fadeIn">
                <h2 class="text-2xl font-bold text-gray-800 mb-6"><i class="fa-solid fa-barcode text-indigo-600 mr-2"></i>Impresión de Etiquetas</h2>
                
                <div class="flex flex-col md:flex-row gap-8">
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm w-full md:w-1/2">
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Seleccionar Equipo del Inventario</label>
                            <select class="w-full border-gray-300 border p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-gray-50">
                                <option>Selecciona un equipo...</option>
                                <option>Proyector Epson Modelo X</option>
                                <option>Cable HDMI 2 Metros</option>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Código Asignado</label>
                            <div class="relative">
                                <i class="fa-solid fa-hashtag absolute left-3 top-3.5 text-gray-400"></i>
                                <input type="text" value="UAS-PRJ-2026-001" class="w-full pl-9 border border-gray-300 p-3 rounded-lg bg-gray-100 text-gray-600 font-mono font-bold" readonly>
                            </div>
                        </div>
                        <button class="bg-gray-800 text-white p-3 rounded-lg w-full hover:bg-gray-900 font-bold flex justify-center items-center gap-2 transition shadow-md">
                            <i class="fa-solid fa-print"></i> Enviar a Impresora Térmica
                        </button>
                    </div>
                    
                    <div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-8">
                        <p class="text-sm font-bold text-gray-500 mb-4 uppercase tracking-widest">Vista Previa de Etiqueta</p>
                        <div class="bg-white p-6 rounded shadow-sm border border-gray-200 flex flex-col items-center">
                            <h4 class="text-xs font-bold text-center mb-2">FACULTAD DE INGENIERÍA MOCHIS</h4>
                            <img src="https://barcode.tec-it.com/barcode.ashx?data=UAS-PRJ-2026-001&code=Code128" alt="Código de Barras" class="h-16 mix-blend-multiply">
                            <p class="text-[10px] mt-1 text-center font-mono">UAS-PRJ-2026-001</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="return-modal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex backdrop-blur-sm transition-opacity">
        <div class="bg-white p-0 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-indigo-600 p-4 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold"><i class="fa-solid fa-clipboard-check mr-2"></i>Finalizar Préstamo</h3>
                <button onclick="document.getElementById('return-modal').classList.add('hidden')" class="text-indigo-200 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <div class="p-6">
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 mb-4 flex items-center">
                    <i class="fa-solid fa-user-graduate text-gray-400 text-2xl mr-3"></i>
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Entregado por:</p>
                        <p id="return-user" class="font-bold text-gray-800 text-lg"></p>
                    </div>
                </div>
                <form id="return-form" method="POST">
                    @csrf <input type="hidden" name="action" value="finish">
                    <label class="block text-sm font-bold mb-2 text-gray-700">Observaciones del equipo devuelto:</label>
                    <textarea name="comment" rows="3" class="w-full border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 p-3 rounded-lg text-sm" placeholder="Ej: Se entregó con todos sus cables completos..."></textarea>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="document.getElementById('return-modal').classList.add('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50">Cancelar</button>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 shadow-md">Confirmar Recepción</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-modal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex backdrop-blur-sm transition-opacity">
        <div class="bg-white p-0 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gray-800 p-4 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold"><i class="fa-solid fa-pen-to-square mr-2"></i>Editar Equipo</h3>
                <button onclick="document.getElementById('edit-modal').classList.add('hidden')" class="text-gray-400 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <div class="p-6">
                <div class="mb-5 flex flex-col items-center justify-center bg-gray-50 py-3 border border-dashed border-gray-300 rounded-lg">
                    <p class="text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Código Asignado</p>
                    <img id="edit-barcode-img" src="" alt="Código" class="h-12 hidden mix-blend-multiply">
                    <p id="edit-barcode-text" class="text-sm font-mono font-bold text-gray-800 mt-1"></p>
                </div>

                <form id="edit-form" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre</label>
                        <input type="text" name="name" id="edit-name" class="w-full border-gray-300 border p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Cantidad Total en Stock</label>
                        <input type="number" name="total_count" id="edit-count" class="w-full border-gray-300 border p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">URL de Fotografía</label>
                        <input type="url" name="photo_url" id="edit-photo" class="w-full border-gray-300 border p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </div>
                    
                    <div class="mb-6 bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="edit-active" class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="ml-3 text-sm font-bold text-gray-700">Equipo Visible en el Catálogo</span>
                        </label>
                        <p class="ml-8 mt-1 text-xs text-gray-500">Desmárcalo si el equipo está en mantenimiento o baja.</p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50">Cancelar</button>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 shadow-md">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="fixed bottom-4 left-4 z-40">
        <button onclick="alert('Centro de Soporte FIM.\nPara modificar plantillas o agregar usuarios administrativos, comuníquese con el depto. de sistemas.')" class="bg-gray-800 text-white p-3 rounded-full shadow-lg hover:bg-gray-700 hover:shadow-xl transition-all duration-300 flex items-center group">
            <i class="fa-solid fa-headset text-xl"></i>
            <span class="max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-300 ease-in-out whitespace-nowrap group-hover:ml-2 font-medium">
                Soporte FIM
            </span>
        </button>
    </div>

    <div id="toast-notification" class="fixed bottom-5 right-5 bg-white border-l-4 border-amber-500 shadow-2xl rounded-lg p-4 transform translate-y-20 opacity-0 transition-all duration-500 z-50 hidden flex items-center gap-4 max-w-sm">
        <div class="bg-amber-100 text-amber-500 p-2 rounded-full">
            <i class="fa-solid fa-bell text-xl"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-gray-800 text-sm">Nueva Solicitud Recibida</h4>
            <p id="toast-message" class="text-sm text-gray-600 mt-0.5">...</p>
        </div>
        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
    </style>

    <script>
        function switchTab(tab) {
            document.getElementById('content-loans').classList.add('hidden');
            document.getElementById('content-history').classList.add('hidden');
            document.getElementById('content-inventory').classList.add('hidden');
            document.getElementById('content-reportes').classList.add('hidden');
            document.getElementById('content-codigos').classList.add('hidden');
            
            const baseClass = "whitespace-nowrap py-3 px-4 rounded-t-lg font-medium text-sm text-gray-500 hover:text-indigo-600 hover:bg-gray-50 transition-colors border-b-2 border-transparent";
            document.getElementById('tab-loans').className = baseClass;
            document.getElementById('tab-history').className = baseClass;
            document.getElementById('tab-inventory').className = baseClass;
            document.getElementById('tab-reportes').className = baseClass;
            document.getElementById('tab-codigos').className = baseClass;

            document.getElementById('content-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).className = "whitespace-nowrap py-3 px-4 rounded-t-lg font-bold text-sm text-indigo-700 bg-indigo-50 border-b-2 border-indigo-600 transition-colors";
        }

        function openReturnModal(id, user, item) {
            document.getElementById('return-user').textContent = user;
            let url = "{{ route('admin.loans.update', ':id') }}".replace(':id', id);
            document.getElementById('return-form').action = url;
            document.getElementById('return-modal').classList.remove('hidden');
        }

        function confirmReject(loanId) {
            let reason = prompt("Por favor, ingrese el motivo del rechazo:");
            if (reason) {
                document.getElementById('comment-reject-' + loanId).value = "RECHAZO: " + reason;
                document.getElementById('form-reject-' + loanId).submit();
            }
        }
        
        function openEditModal(item) {
            document.getElementById('edit-name').value = item.name;
            document.getElementById('edit-count').value = item.total_count;
            document.getElementById('edit-photo').value = item.photo_url || '';
            document.getElementById('edit-active').checked = item.is_active == 1;
            
            if (item.barcode) {
                document.getElementById('edit-barcode-img').src = "https://barcode.tec-it.com/barcode.ashx?data=" + item.barcode + "&code=Code128";
                document.getElementById('edit-barcode-img').classList.remove('hidden');
                document.getElementById('edit-barcode-text').textContent = item.barcode;
            } else {
                document.getElementById('edit-barcode-img').classList.add('hidden');
                document.getElementById('edit-barcode-text').textContent = "Sin código asignado";
            }

            let url = "{{ route('admin.items.update', ':id') }}".replace(':id', item.id);
            document.getElementById('edit-form').action = url;
            document.getElementById('edit-modal').classList.remove('hidden');
        }

        let lastCount = {{ auth()->user()->unreadNotifications->count() }};
        const toast = document.getElementById('toast-notification');
        const msgElement = document.getElementById('toast-message');

        function showToast(message) {
            msgElement.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.remove('translate-y-20', 'opacity-0'); }, 100);
            setTimeout(hideToast, 6000);
        }
        function hideToast() {
            toast.classList.add('translate-y-20', 'opacity-0');
            setTimeout(() => { toast.classList.add('hidden'); }, 500);
        }

        setInterval(() => {
            fetch('{{ route("notifications.check") }}')
                .then(res => res.json())
                .then(data => {
                    if (data.count > lastCount) {
                        lastCount = data.count;
                        if(data.latest) showToast(data.latest.message);
                        setTimeout(() => window.location.reload(), 2500);
                    }
                });
        }, 4000);
    </script>
</body>
</html>