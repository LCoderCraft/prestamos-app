<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Préstamos FIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
        :root { --access-font-size: 100%; }
        body { font-size: var(--access-font-size); }
        .dark body, .dark .bg-gradient-to-br, .dark .bg-gray-50, .dark .bg-gray-100, .dark .bg-gray-200 { background: #1a1a2e !important; }
        .dark .bg-white { background: #16213e !important; }
        .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-600, .dark .text-gray-500,
        .dark .text-gray-400, .dark .text-indigo-600, .dark .text-indigo-700, .dark .text-emerald-700,
        .dark .text-red-600, .dark .text-red-700, .dark .text-amber-800, .dark .text-indigo-900 { color: #e0e0e0 !important; }
        .dark .border-gray-100, .dark .border-gray-200, .dark .border-gray-300 { border-color: #2a2a4a !important; }
        .dark .bg-gray-50 { background: #1a1a2e !important; }
        .dark .bg-indigo-50 { background: #1a1a3e !important; }
        .dark .bg-emerald-50 { background: #0a2a1a !important; }
        .dark .bg-amber-50 { background: #2a2a0a !important; }
        .dark .bg-red-50 { background: #2a0a0a !important; }
        .dark .divide-gray-200 > * { border-color: #2a2a4a !important; }
        .visually-hidden { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
    </style>
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
                <button onclick="toggleAdminDarkMode()" class="text-indigo-300 hover:text-white transition" title="Modo oscuro">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="text-sm text-red-300 hover:text-red-100 font-semibold transition-colors flex items-center gap-2">
                        <span>Salir</span> <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    @php
        $unreadNotifs = auth()->user()->unreadNotifications;
    @endphp
    @if($unreadNotifs->count() > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notifs = @json($unreadNotifs->map(function($n) { return $n->data['message'] ?? 'Nueva solicitud'; }));
            notifs.forEach(function(msg, i) {
                setTimeout(function() {
                    showAdminToast(msg);
                }, i * 1500);
            });
            setTimeout(function() {
                fetch('{{ route("notifications.read") }}').then(function(r) { r.text(); lastCount = 0; });
            }, notifs.length * 1500 + 500);
        });
    </script>
    @endif

    <div class="max-w-7xl mx-auto p-4 mt-2">
        
        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-lg mb-6 shadow-sm border border-emerald-200 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex flex-wrap gap-4 mb-6">
            <a href="{{ route('admin.rooms.index') }}" class="bg-white border border-indigo-200 text-indigo-700 px-4 py-2 rounded-xl font-bold hover:bg-indigo-600 hover:text-white transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-computer"></i> Centros de Cómputo
            </a>
            <a href="{{ route('support.chat.index') }}" class="bg-white border border-emerald-200 text-emerald-700 px-4 py-2 rounded-xl font-bold hover:bg-emerald-600 hover:text-white transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-headset"></i> Chat de Ayuda
            </a>
        </div>

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
                <div><p class="text-sm text-gray-500 font-semibold">Equipos en Inventario</p><p class="text-2xl font-bold text-gray-800"><button onclick="switchTab('inventory')" class="hover:text-indigo-600 transition">Gestionar ➔</button></p></div>
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
                <button onclick="switchTab('rooms')" id="tab-rooms" class="whitespace-nowrap py-3 px-4 rounded-t-lg font-medium text-sm text-gray-500 hover:text-indigo-600 hover:bg-gray-50 transition-colors border-b-2 border-transparent">
                    <i class="fa-solid fa-computer mr-2"></i>Centros de Cómputo
                    <span id="rooms-badge-admin" class="ml-1 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full hidden">0</span>
                </button>
                <button onclick="switchTab('chat')" id="tab-chat" class="whitespace-nowrap py-3 px-4 rounded-t-lg font-medium text-sm text-gray-500 hover:text-indigo-600 hover:bg-gray-50 transition-colors border-b-2 border-transparent">
                    <i class="fa-solid fa-headset mr-2"></i>Chat de Ayuda
                    <span id="chat-badge-admin" class="ml-1 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full hidden">0</span>
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
                        
                        <button onclick='openEditModal(@json($item))' class="w-full bg-gray-100 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 font-semibold py-2 rounded-lg transition-colors border border-gray-200 text-sm">
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
                    <a href="{{ route('admin.reportes.diario') }}" class="group bg-white border-2 border-indigo-100 p-6 rounded-xl hover:border-indigo-500 hover:shadow-lg transition-all block">
                        <div class="bg-indigo-100 text-indigo-600 w-12 h-12 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-calendar-day text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg mb-1">Corte Diario <i class="fa-solid fa-download text-indigo-400 text-sm ml-1"></i></h3>
                        <p class="text-xs text-gray-500">Préstamos completados y pendientes de hoy.</p>
                    </a>
                    <a href="{{ route('admin.reportes.semanal') }}" class="group bg-white border-2 border-indigo-100 p-6 rounded-xl hover:border-indigo-500 hover:shadow-lg transition-all block">
                        <div class="bg-indigo-100 text-indigo-600 w-12 h-12 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-calendar-week text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg mb-1">Resumen Semanal <i class="fa-solid fa-download text-indigo-400 text-sm ml-1"></i></h3>
                        <p class="text-xs text-gray-500">Estadísticas de uso de los últimos 7 días.</p>
                    </a>
                    <a href="{{ route('admin.reportes.mensual') }}" class="group bg-white border-2 border-indigo-100 p-6 rounded-xl hover:border-indigo-500 hover:shadow-lg transition-all block">
                        <div class="bg-indigo-100 text-indigo-600 w-12 h-12 rounded-full flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-chart-line text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg mb-1">Métricas Mensuales <i class="fa-solid fa-download text-indigo-400 text-sm ml-1"></i></h3>
                        <p class="text-xs text-gray-500">Equipos más usados y usuarios frecuentes.</p>
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    @php
                        $todayLoans = $activeLoans->merge($historyLoans)->filter(function($l) {
                            return $l->created_at->isToday();
                        });
                        $todayReservations = App\Models\RoomReservation::whereDate('created_at', today())->count();
                        $topItem = $historyLoans->groupBy('item_id')->sortByDesc(function($g) { return $g->count(); })->first();
                        $topItemName = $topItem && $topItem->first()?->item?->name ? $topItem->first()->item->name : 'N/A';
                        $todayPending = $activeLoans->where('status', 'pending')->filter(function($l) { return $l->created_at->isToday(); })->count();
                    @endphp
                    <div class="bg-white border border-gray-200 p-4 rounded-xl text-center shadow-sm">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Préstamos Hoy</p>
                        <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $todayLoans->count() }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 p-4 rounded-xl text-center shadow-sm">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Reservaciones Hoy</p>
                        <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $todayReservations }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 p-4 rounded-xl text-center shadow-sm">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Pendientes Hoy</p>
                        <p class="text-3xl font-bold text-amber-600 mt-1">{{ $todayPending }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 p-4 rounded-xl text-center shadow-sm">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Equipo Más Usado</p>
                        <p class="text-sm font-bold text-gray-800 mt-1 truncate">{{ $topItemName }}</p>
                    </div>
                </div>
            </div>

            <div id="content-codigos" class="hidden animate-fadeIn">
                <h2 class="text-2xl font-bold text-gray-800 mb-6"><i class="fa-solid fa-barcode text-indigo-600 mr-2"></i>Impresión de Etiquetas</h2>
                
                <div class="flex flex-col md:flex-row gap-8">
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm w-full md:w-1/2">
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Seleccionar Equipo del Inventario</label>
                            <select id="admin-barcode-select" class="w-full border-gray-300 border p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-gray-50" onchange="updateAdminPreview()">
                                <option value="">-- Selecciona un equipo --</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" data-barcode="{{ $item->barcode ?? '' }}" data-name="{{ $item->name }}">
                                        {{ $item->name }} {{ $item->barcode ? '('.$item->barcode.')' : '(Sin código)' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Código Asignado</label>
                            <div class="relative">
                                <i class="fa-solid fa-hashtag absolute left-3 top-3.5 text-gray-400"></i>
                                <input type="text" id="admin-barcode-display" value="" class="w-full pl-9 border border-gray-300 p-3 rounded-lg bg-gray-100 text-gray-600 font-mono font-bold" readonly>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button id="admin-btn-print" onclick="adminPrintBarcode()" class="bg-gray-800 text-white p-3 rounded-lg w-full hover:bg-gray-900 font-bold flex justify-center items-center gap-2 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                <i class="fa-solid fa-print"></i> Imprimir Etiqueta
                            </button>
                            <button id="admin-btn-generate" onclick="adminGenerateBarcode()" class="bg-indigo-600 text-white p-3 rounded-lg hover:bg-indigo-700 font-bold flex justify-center items-center gap-2 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                <i class="fa-solid fa-arrows-rotate"></i> Generar
                            </button>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-8">
                        <p class="text-sm font-bold text-gray-500 mb-4 uppercase tracking-widest">Vista Previa de Etiqueta</p>
                        <div id="admin-preview-empty" class="text-gray-400 text-sm italic text-center">
                            <i class="fa-solid fa-barcode text-5xl block mb-2 text-gray-300"></i>
                            Selecciona un equipo
                        </div>
                        <div id="admin-preview-content" class="hidden bg-white p-6 rounded shadow-sm border border-gray-200 flex flex-col items-center">
                            <h4 class="text-xs font-bold text-center mb-2">FACULTAD DE INGENIERÍA MOCHIS</h4>
                            <img id="admin-barcode-img" src="" alt="Código de Barras" class="h-16 mix-blend-multiply">
                            <p id="admin-barcode-label" class="text-[10px] mt-1 text-center font-mono"></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Inventario Completo</h3>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Equipo</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Código</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Stock</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item->name }}</td>
                                    <td class="px-4 py-3 font-mono text-indigo-600 font-bold">{{ $item->barcode ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $item->total_count }} unid.</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="content-rooms" class="hidden animate-fadeIn">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800"><i class="fa-solid fa-computer text-indigo-600 mr-2"></i>Centros de Cómputo</h2>
                    <a href="{{ route('admin.rooms.index') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-arrow-right"></i> Ir a gestión completa
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    @php
                        $rooms = App\Models\ComputerRoom::all();
                    @endphp
                    @forelse($rooms as $room)
                        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="bg-indigo-100 text-indigo-600 p-2.5 rounded-lg">
                                    <i class="fa-solid fa-display text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $room->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $room->capacity }} equipos</p>
                                </div>
                            </div>
                            <div class="text-xs text-gray-400">{{ $room->location ?? 'Sin ubicación' }}</div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8 text-gray-400">
                            <i class="fa-solid fa-building text-4xl mb-2"></i>
                            <p>No hay centros de cómputo registrados</p>
                            <a href="{{ route('admin.rooms.index') }}" class="text-indigo-600 text-sm font-medium mt-2 inline-block">Gestionar →</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div id="content-chat" class="hidden animate-fadeIn">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800"><i class="fa-solid fa-headset text-indigo-600 mr-2"></i>Chat de Ayuda</h2>
                    <a href="{{ route('support.chat.index') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-comment-dots"></i> Ir al chat completo
                    </a>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    @php
                        $chats = App\Models\SupportChat::with(['user', 'lastMessage'])
                            ->where('status', 'open')
                            ->orderBy('last_message_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    @forelse($chats as $chat)
                        <a href="{{ route('admin.support.chat', $chat->id) }}" class="flex items-start gap-4 p-4 border-b border-gray-100 hover:bg-gray-50 transition group">
                            <div class="bg-indigo-100 text-indigo-600 p-2.5 rounded-full flex-shrink-0">
                                <i class="fa-solid fa-user text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <span class="font-bold text-gray-800 text-sm">{{ $chat->user->username }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : '' }}</span>
                                </div>
                                <p class="text-xs text-gray-500 truncate">{{ $chat->subject }}</p>
                                <p class="text-xs text-gray-400 truncate mt-0.5">{{ $chat->lastMessage?->body ?? 'Sin mensajes' }}</p>
                            </div>
                            @php $unread = $chat->unreadAdminMessages()->count(); @endphp
                            @if($unread > 0)
                                <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full flex-shrink-0">{{ $unread }}</span>
                            @endif
                        </a>
                    @empty
                        <div class="text-center py-10 text-gray-400">
                            <i class="fa-solid fa-inbox text-4xl mb-2"></i>
                            <p class="text-sm">No hay chats activos</p>
                        </div>
                    @endforelse
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
        <a href="{{ route('support.chat.index') }}" class="bg-gray-800 text-white p-3 rounded-full shadow-lg hover:bg-gray-700 hover:shadow-xl transition-all duration-300 flex items-center group">
            <i class="fa-solid fa-headset text-xl"></i>
            <span class="max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-300 ease-in-out whitespace-nowrap group-hover:ml-2 font-medium">
                Soporte FIM
            </span>
        </a>
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
            const tabs = ['loans', 'history', 'inventory', 'reportes', 'codigos', 'rooms', 'chat'];
            tabs.forEach(t => {
                document.getElementById('content-' + t)?.classList.add('hidden');
                const el = document.getElementById('tab-' + t);
                if (el) {
                    el.className = "whitespace-nowrap py-3 px-4 rounded-t-lg font-medium text-sm text-gray-500 hover:text-indigo-600 hover:bg-gray-50 transition-colors border-b-2 border-transparent";
                }
            });
            document.getElementById('content-' + tab)?.classList.remove('hidden');
            document.getElementById('tab-' + tab).className = "whitespace-nowrap py-3 px-4 rounded-t-lg font-bold text-sm text-indigo-700 bg-indigo-50 border-b-2 border-indigo-600 transition-colors";
        }
        function openReturnModal(id, user, item) {
            document.getElementById('return-user').textContent = user;
            document.getElementById('return-form').action = "{{ route('admin.loans.update', ':id') }}".replace(':id', id);
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
            document.getElementById('edit-form').action = "{{ route('admin.items.update', ':id') }}".replace(':id', item.id);
            document.getElementById('edit-modal').classList.remove('hidden');
        }
        // --- REPRODUCIR SONIDO ---
        function playNotificationSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                // Sonido tipo "ding-ding"
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                osc.frequency.setValueAtTime(660, ctx.currentTime + 0.12);
                osc.frequency.setValueAtTime(880, ctx.currentTime + 0.24);
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
                osc.start();
                osc.stop(ctx.currentTime + 0.5);
            } catch (e) { /* fallo silencioso */ }
        }
        // --- TOAST DE NOTIFICACIONES ---
        let lastCount = {{ auth()->user()->unreadNotifications->count() ?? 0 }};
        let lastChatCount = 0;
        const toast = document.getElementById('toast-notification');
        const msgElement = document.getElementById('toast-message');
        function showAdminToast(message, isChat = false) {
            if (!toast) return;
            toast.className = 'fixed bottom-5 right-5 shadow-2xl rounded-lg p-4 transform translate-y-20 opacity-0 transition-all duration-500 z-50 flex items-center gap-4 max-w-sm';
            if (isChat) {
                toast.classList.add('bg-purple-50', 'border-l-4', 'border-purple-500');
            } else {
                toast.classList.add('bg-white', 'border-l-4', 'border-amber-500');
            }
            if (!isChat && localStorage.getItem('visualIndicators') !== 'false') {
                document.body.style.transition = 'background 0.3s';
                document.body.style.background = 'rgba(99,102,241,0.15)';
                setTimeout(function() { document.body.style.background = ''; }, 500);
            }
            msgElement.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.remove('translate-y-20', 'opacity-0'); }, 100);
            setTimeout(hideAdminToast, 10000);
        }
        function hideAdminToast() {
            if (!toast) return;
            toast.classList.add('translate-y-20', 'opacity-0');
            setTimeout(() => { toast.classList.add('hidden'); }, 500);
        }
        // --- POLLING DE NOTIFICACIONES (préstamos + chat + rooms) ---
        setInterval(() => {
            // Notificaciones de Laravel (préstamos)
            fetch('{{ route("notifications.check") }}')
                .then(res => res.json())
                .then(data => {
                    if (data.count > lastCount) {
                        lastCount = data.count;
                        if (data.latest) {
                            showAdminToast(data.latest.message, false);
                            playNotificationSound();
                        }
                        setTimeout(() => window.location.reload(), 2500);
                    }
                })
                .catch(() => {});
            // Notificaciones de chat (sonido + badge)
            fetch('{{ route("support.chat.unread") }}')
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('chat-badge-admin');
                    if (data.count > 0) {
                        if (badge) { badge.classList.remove('hidden'); badge.textContent = data.count; }
                        if (data.count > lastChatCount) {
                            lastChatCount = data.count;
                            showAdminToast('Nuevo mensaje de chat de ayuda', true);
                            playNotificationSound();
                        }
                    } else {
                        if (badge) badge.classList.add('hidden');
                    }
                })
                .catch(() => {});
            // Notificaciones de reservaciones (badge)
            fetch('/admin/rooms/pending-count')
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('rooms-badge-admin');
                    if (data.count > 0) {
                        if (badge) { badge.classList.remove('hidden'); badge.textContent = data.count; }
                    } else {
                        if (badge) badge.classList.add('hidden');
                    }
                })
                .catch(() => {});
        }, 4000);
        // Inicializar lastChatCount
        fetch('{{ route("support.chat.unread") }}').then(r => r.json()).then(d => { lastChatCount = d.count; }).catch(() => {});

        // --- DARK MODE ADMIN ---
        (function() {
            if (localStorage.getItem('adminDarkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
        function toggleAdminDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('adminDarkMode', isDark);
        }

        // --- CÓDIGOS DE BARRAS (Admin) ---
        let adminSelectedBarcode = '';

        function updateAdminPreview() {
            const select = document.getElementById('admin-barcode-select');
            const option = select.options[select.selectedIndex];
            const barcode = option.dataset.barcode;

            const previewEmpty = document.getElementById('admin-preview-empty');
            const previewContent = document.getElementById('admin-preview-content');
            const barcodeImg = document.getElementById('admin-barcode-img');
            const barcodeDisplay = document.getElementById('admin-barcode-display');
            const barcodeLabel = document.getElementById('admin-barcode-label');
            const btnPrint = document.getElementById('admin-btn-print');
            const btnGenerate = document.getElementById('admin-btn-generate');

            adminSelectedBarcode = barcode;

            if (!option.value || !barcode) {
                previewEmpty.classList.remove('hidden');
                previewContent.classList.add('hidden');
                barcodeDisplay.value = barcode || '';
                btnPrint.disabled = true;
                btnGenerate.disabled = !option.value;
                return;
            }

            previewEmpty.classList.add('hidden');
            previewContent.classList.remove('hidden');
            barcodeImg.src = 'https://barcode.tec-it.com/barcode.ashx?data=' + barcode + '&code=Code128';
            barcodeDisplay.value = barcode;
            barcodeLabel.textContent = barcode;
            btnPrint.disabled = false;
            btnGenerate.disabled = false;
        }

        function adminPrintBarcode() {
            if (!adminSelectedBarcode) return;
            const win = window.open('', '_blank');
            win.document.write(`
                <html><head><title>Etiqueta</title>
                <style>
                    body { display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: sans-serif; }
                    .label { text-align: center; padding: 20px; border: 1px solid #ccc; }
                    h3 { font-size: 12px; margin: 0 0 8px; text-transform: uppercase; }
                    img { height: 60px; }
                    p { font-size: 11px; font-family: monospace; margin-top: 4px; }
                </style></head>
                <body>
                    <div class="label">
                        <h3>Facultad de Ingeniería Mochis</h3>
                        <img src="https://barcode.tec-it.com/barcode.ashx?data=${adminSelectedBarcode}&code=Code128" alt="Código">
                        <p>${adminSelectedBarcode}</p>
                    </div>
                    <script>window.print();window.close();<\/script>
                </body></html>
            `);
            win.document.close();
        }

        function adminGenerateBarcode() {
            const select = document.getElementById('admin-barcode-select');
            const id = select.value;
            if (!id) return;
            fetch('{{ url("admin/codigos") }}/' + id + '/regenerar', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const opt = select.options[select.selectedIndex];
                        opt.text = data.name + ' (' + data.barcode + ')';
                        opt.dataset.barcode = data.barcode;
                        updateAdminPreview();
                        showAdminToast('Código regenerado: ' + data.barcode);
                    }
                });
        }
    </script>
</body>
</html>