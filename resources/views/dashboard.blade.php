<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Préstamos - FIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }
        :root { --access-font-size: 100%; --access-contrast: 0; }
        .high-contrast { --access-contrast: 1; }
        .high-contrast * { border-color: currentColor !important; }
        .high-contrast .bg-white, .high-contrast .bg-gray-50, .high-contrast .bg-indigo-50,
        .high-contrast .bg-emerald-50, .high-contrast .bg-amber-50, .high-contrast .bg-red-50 { background-color: #000 !important; }
        .high-contrast .text-gray-800, .high-contrast .text-gray-700, .high-contrast .text-gray-600,
        .high-contrast .text-gray-500, .high-contrast .text-indigo-600, .high-contrast .text-indigo-700,
        .high-contrast .text-emerald-700, .high-contrast .text-red-600, .high-contrast .text-amber-800,
        .high-contrast .text-indigo-200, .high-contrast .text-indigo-100 { color: #fff !important; }
        .high-contrast .border-gray-100, .high-contrast .border-gray-200, .high-contrast .border-gray-300 { border-color: #333 !important; }
        .high-contrast nav { background: #000 !important; border-color: #fff !important; }
        .high-contrast .bg-indigo-800 { background: #000 !important; }
        .high-contrast .bg-indigo-100 { background: #111 !important; }
        .high-contrast .bg-emerald-50 { background: #000 !important; }
        body { font-size: var(--access-font-size); }
        .visually-hidden { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
        .dark body, .dark .bg-gradient-to-br, .dark .bg-gray-50, .dark .bg-gray-100, .dark .bg-gray-200 { background: #1a1a2e !important; }
        .dark .bg-white { background: #16213e !important; }
        .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-600, .dark .text-gray-500,
        .dark .text-gray-400, .dark .text-indigo-600, .dark .text-indigo-700, .dark .text-emerald-700,
        .dark .text-red-600, .dark .text-red-700 { color: #e0e0e0 !important; }
        .dark .border-gray-100, .dark .border-gray-200, .dark .border-gray-300 { border-color: #2a2a4a !important; }
        .dark .bg-gray-50 { background: #1a1a2e !important; }
        .dark .bg-indigo-50 { background: #1a1a3e !important; }
        .dark .bg-emerald-50 { background: #0a2a1a !important; }
        .dark .bg-amber-50 { background: #2a2a0a !important; }
        .dark .bg-red-50 { background: #2a0a0a !important; }
        .dark .divide-gray-100 > *, .dark .divide-gray-200 > * { border-color: #2a2a4a !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 min-h-screen font-sans">
    
    <nav class="bg-indigo-800 shadow-lg border-b-4 border-indigo-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-white text-indigo-800 p-2 rounded-lg shadow-sm">
                    <i class="fa-solid fa-projector text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-wide">Catálogo FIM</h1>
                    <p class="text-xs text-indigo-200 hidden md:block">Sistema de Control de Préstamos</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-1 md:space-x-3">
                <button onclick="toggleQuickDarkMode()" class="text-indigo-300 hover:text-white p-2 rounded-lg hover:bg-indigo-700 transition hidden md:block" title="Modo oscuro">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <button onclick="showHelpFIM()" class="text-indigo-300 hover:text-white p-2 rounded-lg hover:bg-indigo-700 transition" title="Ayuda FIM">
                    <i class="fa-solid fa-circle-info"></i>
                </button>
                <button onclick="openProfileModal()" class="flex items-center space-x-2 bg-indigo-900/50 hover:bg-indigo-700 px-4 py-2 rounded-full border border-indigo-600 transition-colors text-indigo-100 hover:text-white">
                    <i class="fa-solid fa-circle-user text-lg"></i>
                    <span class="font-bold text-sm">{{ Auth::user()->username }}</span>
                    <i class="fa-solid fa-chevron-down text-xs ml-1"></i>
                </button>

                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="text-sm text-red-300 hover:text-red-100 hover:bg-red-900/30 p-2 md:px-3 md:py-2 rounded-lg font-semibold transition-colors flex items-center gap-2 border border-transparent hover:border-red-800">
                        <span class="hidden md:inline">Salir</span> <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-4 mt-4 animate-fadeIn">
        
        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-lg mb-6 shadow-sm border border-emerald-200 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 shadow-sm border border-red-200 flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-red-500 text-lg"></i>
                <span class="font-medium">{!! session('error') !!}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-amber-50 text-amber-800 p-4 rounded-lg mb-6 shadow-sm border border-amber-200 flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-amber-500 text-lg mt-0.5"></i>
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-10">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fa-solid fa-boxes-stacked text-indigo-600 mr-3"></i>Equipos Disponibles
                </h2>
                <div class="flex gap-2" role="group" aria-label="Vista de inventario">
                    <button id="view-grid" onclick="setView('grid')" class="px-3 py-2 rounded-lg font-bold text-sm border transition-colors bg-indigo-600 text-white border-indigo-600" aria-pressed="true" title="Vista cuadrícula">
                        <i class="fa-solid fa-th"></i>
                    </button>
                    <button id="view-list" onclick="setView('list')" class="px-3 py-2 rounded-lg font-bold text-sm border transition-colors bg-white text-gray-700 border-gray-300 hover:bg-gray-50" aria-pressed="false" title="Vista lista con detalles">
                        <i class="fa-solid fa-list"></i>
                    </button>
                </div>
            </div>
            
            <div class="flex gap-3 mb-6 flex-wrap">
                <a href="{{ route('rooms.index') }}" class="bg-white border border-indigo-200 text-indigo-700 px-5 py-3 rounded-xl font-bold hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-colors shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-computer"></i> Reservar Centro de Computo
                </a>
                <a href="{{ route('support.chat.index') }}" class="bg-white border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl font-bold hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-colors shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-headset"></i> Chat de Ayuda
                </a>
            </div>

            {{-- Vista cuadrícula --}}
            <div id="inventory-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($items as $item)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col overflow-hidden group">
                    <div class="h-40 bg-gray-50 flex items-center justify-center border-b border-gray-100 overflow-hidden relative">
                        @if($item->photo_url)
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $item->photo_url }}" alt="{{ $item->name }}"> 
                        @else
                            <i class="fa-solid fa-box-open text-5xl text-gray-300 group-hover:text-indigo-300 transition-colors"></i>
                        @endif
                        <div class="absolute top-2 right-2 bg-emerald-100 text-emerald-700 text-xs font-bold px-2 py-1 rounded-full shadow-sm">
                            Disponible
                        </div>
                    </div>
                    <div class="p-5 flex flex-col flex-grow justify-between">
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg mb-1">{{ $item->name }}</h3>
                            <p class="text-xs text-gray-500 mb-1">Equipo propiedad de la Facultad de Ingeniería Mochis.</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $item->barcode ?? 'Sin código' }}</p>
                        </div>
                        <button onclick="openModal('{{ $item->id }}', '{{ $item->name }}')" class="w-full bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold py-2 px-4 rounded-lg hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-colors shadow-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-hand-holding-hand"></i> Solicitar
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Vista lista --}}
            <div id="inventory-list" class="hidden">
                <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Foto</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Equipo</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Código</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Estado</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($items as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    @if($item->photo_url)
                                        <img src="{{ $item->photo_url }}" alt="{{ $item->name }}" class="h-10 w-10 object-cover rounded-lg">
                                    @else
                                        <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-box-open text-gray-400"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-800">{{ $item->name }}</div>
                                    <div class="text-xs text-gray-400">Stock: {{ $item->total_count }} unid.</div>
                                </td>
                                <td class="px-4 py-3 font-mono text-indigo-600 font-bold text-xs">{{ $item->barcode ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-[10px] uppercase font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        {{ $item->is_active ? 'Disponible' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button onclick="openModal('{{ $item->id }}', '{{ $item->name }}')" class="bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold py-2 px-4 rounded-lg hover:bg-indigo-600 hover:text-white transition-colors text-xs inline-flex items-center gap-1">
                                        <i class="fa-solid fa-hand-holding-hand"></i> Solicitar
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                    <i class="fa-solid fa-clock-rotate-left text-indigo-600 mr-2"></i>Mis Solicitudes Actuales
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-gray-500 text-xs uppercase font-bold">Equipo</th>
                                <th class="px-4 py-3 text-gray-500 text-xs uppercase font-bold">Horario</th>
                                <th class="px-4 py-3 text-gray-500 text-xs uppercase font-bold">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($myLoans->where('status', '!=', 'finished') as $loan)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-800 text-sm">{{ $loan->item->name }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    <div class="font-bold">{{ $loan->start_date->format('d/m/Y') }}</div>
                                    <div>{{ $loan->start_date->format('H:i') }} - {{ $loan->end_date->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-[10px] uppercase tracking-wider font-bold rounded-full 
                                    {{ $loan->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 
                                      ($loan->status === 'rejected' ? 'bg-red-100 text-red-800 border border-red-200' : 
                                      ($loan->status === 'pending' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-gray-100 text-gray-800')) }}">
                                        @if($loan->status=='pending') <i class="fa-solid fa-hourglass-half mr-1"></i> Pendiente
                                        @elseif($loan->status=='active') <i class="fa-solid fa-circle-play mr-1"></i> Activo
                                        @elseif($loan->status=='rejected') <i class="fa-solid fa-ban mr-1"></i> Rechazado
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            @if($loan->admin_comment && $loan->status == 'rejected')
                            <tr class="bg-red-50/50"><td colspan="3" class="px-4 py-2 text-xs text-red-600 italic border-t-0"><i class="fa-solid fa-comment-dots mr-1"></i>Motivo: {{ $loan->admin_comment }}</td></tr>
                            @endif
                            @endforeach

                            @forelse($myRooms as $res)
                            <tr class="hover:bg-gray-50 transition-colors bg-indigo-50/30">
                                <td class="px-4 py-3 font-medium text-gray-800 text-sm flex items-center gap-1">
                                    <i class="fa-solid fa-computer text-indigo-400 text-xs"></i>
                                    {{ $res->computerRoom->name }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    <div class="font-bold">{{ $res->start_date->format('d/m/Y') }}</div>
                                    <div>{{ $res->start_date->format('H:i') }} - {{ $res->end_date->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-[10px] uppercase tracking-wider font-bold rounded-full 
                                    {{ $res->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 
                                      'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                        @if($res->status=='pending') <i class="fa-solid fa-hourglass-half mr-1"></i> Pendiente
                                        @elseif($res->status=='active') <i class="fa-solid fa-circle-play mr-1"></i> Activo
                                        @endif
                                    </span>
                                </td>
                            </tr>
                            @empty
                            @endforelse

                            @if($myLoans->where('status', '!=', 'finished')->count() == 0 && $myRooms->count() == 0)
                            <tr>
                                <td colspan="3" class="px-4 py-10 text-center">
                                    <i class="fa-solid fa-box-open text-gray-300 text-4xl mb-2"></i>
                                    <p class="text-sm text-gray-500 font-medium">No tienes solicitudes activas o en espera.</p>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 flex items-center">
                    <i class="fa-solid fa-clipboard-check text-gray-500 mr-2"></i>Historial de Devoluciones
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse opacity-90">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-gray-400 text-xs uppercase font-bold">Equipo</th>
                                <th class="px-4 py-3 text-gray-400 text-xs uppercase font-bold">Fecha</th>
                                <th class="px-4 py-3 text-gray-400 text-xs uppercase font-bold">Observación Final</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php $history = $myLoans->where('status', 'finished'); @endphp
                            @forelse($history as $loan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700 font-medium">{{ $loan->item->name }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $loan->start_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500 italic">
                                    {{ str_replace('DEVOLUCIÓN: ', '', $loan->admin_comment) ?: 'Entregado sin novedades' }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-10 text-center text-sm text-gray-400 italic">Tu historial de préstamos está vacío.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div id="loan-modal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex backdrop-blur-sm transition-opacity">
        <div class="bg-white p-0 rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform scale-100 transition-transform">
            <div class="bg-indigo-600 p-4 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold flex items-center"><i class="fa-solid fa-calendar-plus mr-2"></i>Nueva Solicitud</h3>
                <button onclick="document.getElementById('loan-modal').classList.add('hidden')" class="text-indigo-200 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <form action="{{ route('loans.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="item_id" id="modal-item-id">
                <div class="mb-4 bg-indigo-50 p-3 rounded-lg border border-indigo-100">
                    <p class="text-xs text-indigo-800 uppercase font-bold mb-1">Equipo Seleccionado</p>
                    <p id="modal-title" class="font-bold text-gray-800"></p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-700">Fecha Requerida</label>
                        <input type="date" name="date" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-700">Hora de Inicio</label>
                        <input type="time" name="time" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" required>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Duración Estimada</label>
                    <div class="relative">
                        <i class="fa-solid fa-stopwatch absolute left-3 top-3.5 text-gray-400"></i>
                        <select name="duration" class="w-full pl-9 border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm appearance-none bg-white" required>
                            <option value="1">1 Hora</option>
                            <option value="2">2 Horas</option>
                            <option value="3">3 Horas (Medio Turno)</option>
                            <option value="4">4 Horas</option>
                            <option value="5">5 Horas (Turno Completo)</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-3.5 text-gray-400 pointer-events-none text-xs"></i>
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('loan-modal').classList.add('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 shadow-md transition">Confirmar Solicitud</button>
                </div>
            </form>
        </div>
    </div>

    <div id="profile-modal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex backdrop-blur-sm transition-opacity">
        <div class="bg-white p-0 rounded-xl shadow-2xl w-full max-w-lg overflow-y-auto max-h-[90vh] relative">
            
            <div class="bg-gray-800 p-4 text-white flex justify-between items-center sticky top-0 z-10">
                <h3 class="text-lg font-bold flex items-center"><i class="fa-solid fa-user-gear mr-2"></i>Configuración de Cuenta</h3>
                <button onclick="document.getElementById('profile-modal').classList.add('hidden')" class="text-gray-400 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <form action="{{ route('profile.update_credentials') }}" method="POST" class="p-6 border-b border-gray-200">
                @csrf @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Correo Electrónico (Contacto)</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3 top-3 text-gray-400"></i>
                        <input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full pl-9 border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" required>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                    <p class="text-xs font-bold text-gray-500 uppercase mb-3"><i class="fa-solid fa-shield-halved mr-1"></i>Cambio de Contraseña</p>
                    <div class="mb-3">
                        <input type="password" name="new_password" placeholder="Nueva Contraseña (Mínimo 8 caract.)" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </div>
                    <div>
                        <input type="password" name="new_password_confirmation" placeholder="Confirmar Nueva Contraseña" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2">* Deja estos campos en blanco si no deseas cambiar tu contraseña.</p>
                </div>

                <div class="mb-4 bg-red-50 p-4 rounded-lg border border-red-100">
                    <label class="block text-sm font-bold mb-1 text-red-800"><i class="fa-solid fa-lock mr-1"></i>Contraseña Actual</label>
                    <p class="text-xs text-red-600 mb-2">Requerida por seguridad para guardar cualquier cambio.</p>
                    <input type="password" name="current_password" placeholder="Ingresa tu contraseña actual" class="w-full border border-red-300 p-2.5 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm" required>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('profile-modal').classList.add('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 shadow-md transition">Guardar Perfil</button>
                </div>
            </form>

            {{-- ACCESIBILIDAD --}}
            <div class="p-6 border-b border-gray-200">
                <h4 class="text-md font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fa-solid fa-universal-access text-indigo-600 mr-2"></i>Accesibilidad
                </h4>

                <div class="space-y-5">
                    {{-- Modo oscuro --}}
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-sm text-gray-700" for="dark-mode-toggle">Modo Oscuro</label>
                            <p class="text-xs text-gray-400">Reduce la fatiga visual en entornos con poca luz.</p>
                        </div>
                        <button id="dark-mode-toggle" onclick="toggleDarkMode()" role="switch" aria-checked="false" class="relative w-12 h-6 rounded-full bg-gray-300 transition-colors">
                            <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"></span>
                        </button>
                    </div>

                    {{-- Alto contraste --}}
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-sm text-gray-700" for="contrast-toggle">Alto Contraste</label>
                            <p class="text-xs text-gray-400">Mejora la legibilidad con colores de alto contraste.</p>
                        </div>
                        <button id="contrast-toggle" onclick="toggleContrast()" role="switch" aria-checked="false" class="relative w-12 h-6 rounded-full bg-gray-300 transition-colors">
                            <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"></span>
                        </button>
                    </div>

                    {{-- Tamaño de fuente --}}
                    <div>
                        <label class="font-medium text-sm text-gray-700 block mb-2" for="font-size-slider">
                            Tamaño de Fuente: <span id="font-size-label">100%</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-minus text-xs text-gray-400"></i>
                            <input id="font-size-slider" type="range" min="80" max="150" value="100" step="5" class="w-full accent-indigo-600" oninput="setFontSize(this.value)">
                            <i class="fa-solid fa-plus text-xs text-gray-400"></i>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Ajusta el tamaño del texto para una lectura más cómoda.</p>
                    </div>

                    {{-- Indicadores visuales --}}
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="font-medium text-sm text-gray-700" for="visual-indicator-toggle">Indicadores Visuales</label>
                            <p class="text-xs text-gray-400">Muestra notificaciones visuales además del sonido (útil si tienes dificultades auditivas).</p>
                        </div>
                        <button id="visual-indicator-toggle" onclick="toggleVisualIndicators()" role="switch" aria-checked="true" class="relative w-12 h-6 rounded-full bg-indigo-500 transition-colors">
                            <span class="absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- GUÍA DE USO --}}
            <div class="p-6">
                <h4 class="text-md font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fa-solid fa-circle-question text-indigo-600 mr-2"></i>¿Cómo usar el sistema?
                </h4>
                
                <div class="space-y-3 text-sm text-gray-600">
                    <details class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <summary class="font-bold text-gray-700 cursor-pointer">📦 Solicitar un equipo</summary>
                        <div class="mt-2 pl-4 text-xs space-y-1">
                            <p>1. Busca el equipo que necesitas en la sección "Equipos Disponibles".</p>
                            <p>2. Haz clic en "Solicitar" y elige fecha, hora y duración.</p>
                            <p>3. Espera a que un administrador apruebe tu solicitud.</p>
                            <p>4. Recibirás una notificación cuando tu préstamo esté activo.</p>
                        </div>
                    </details>

                    <details class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <summary class="font-bold text-gray-700 cursor-pointer">🖥️ Reservar un Centro de Cómputo</summary>
                        <div class="mt-2 pl-4 text-xs space-y-1">
                            <p>1. Haz clic en "Reservar Centro de Cómputo".</p>
                            <p>2. Selecciona el centro que deseas y revisa la disponibilidad en el calendario.</p>
                            <p>3. Haz clic en "Nueva Reservación" y completa los datos.</p>
                            <p>4. Puedes reservar como individual, grupo escolar o profesor.</p>
                        </div>
                    </details>

                    <details class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <summary class="font-bold text-gray-700 cursor-pointer">💬 Chat de Ayuda</summary>
                        <div class="mt-2 pl-4 text-xs space-y-1">
                            <p>1. Haz clic en "Chat de Ayuda" para iniciar una conversación.</p>
                            <p>2. Escribe un asunto y describe tu problema o duda.</p>
                            <p>3. Recibirás respuesta de un administrador en tiempo real.</p>
                            <p>4. Recibirás notificaciones cuando haya nuevos mensajes.</p>
                        </div>
                    </details>

                    <details class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <summary class="font-bold text-gray-700 cursor-pointer">⚙️ Personalizar vista</summary>
                        <div class="mt-2 pl-4 text-xs space-y-1">
                            <p>• Cambia entre vista cuadrícula y lista con los botones sobre el inventario.</p>
                            <p>• Activa el modo oscuro desde esta configuración.</p>
                            <p>• Ajusta el tamaño de fuente y contraste para mejor visibilidad.</p>
                            <p>• Los cambios se guardan automáticamente en tu navegador.</p>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed bottom-4 right-4 z-40">
        <button onclick="showHelpFIM()" class="bg-gray-800 text-white p-3 rounded-full shadow-lg hover:bg-gray-700 hover:shadow-xl transition-all duration-300 flex items-center group">
            <i class="fa-solid fa-circle-info text-xl"></i>
            <span class="max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-300 ease-in-out whitespace-nowrap group-hover:ml-2 font-medium">
                Ayuda FIM
            </span>
        </button>
        
        <div id="help-modal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
                <div class="bg-gray-800 p-4 text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold flex items-center"><i class="fa-solid fa-circle-info mr-2"></i>Centro de Servicios FIM</h3>
                    <button onclick="document.getElementById('help-modal').classList.add('hidden')" class="text-gray-400 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                        <h4 class="font-bold text-indigo-800 mb-2"><i class="fa-solid fa-location-dot mr-1"></i> Ubicación</h4>
                        <p class="text-gray-700">Facultad de Ingeniería Mochis</p>
                        <p class="text-gray-600 text-xs">Centro de Cómputo, Edificio Principal, Planta Baja</p>
                        <p class="text-gray-600 text-xs mt-1">Por la entrada principal, frente a la biblioteca.</p>
                    </div>
                    <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-100">
                        <h4 class="font-bold text-emerald-800 mb-2"><i class="fa-solid fa-phone mr-1"></i> Contacto</h4>
                        <p class="text-gray-700"><strong>Teléfono:</strong> (668) 123-4567</p>
                        <p class="text-gray-700"><strong>Ext:</strong> 1234</p>
                        <p class="text-gray-600 text-xs mt-1">Horario: Lunes a Viernes 7:00 - 19:00 hrs</p>
                    </div>
                    <div class="bg-amber-50 p-4 rounded-lg border border-amber-100">
                        <h4 class="font-bold text-amber-800 mb-2"><i class="fa-solid fa-user-tie mr-1"></i> Encargados</h4>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-center gap-2"><span class="w-2 h-2 bg-amber-500 rounded-full"></span> Luis Humberto — Coordinador</li>
                            <li class="flex items-center gap-2"><span class="w-2 h-2 bg-amber-500 rounded-full"></span> Ángel Verdugo — Soporte Técnico</li>
                            <li class="flex items-center gap-2"><span class="w-2 h-2 bg-amber-500 rounded-full"></span> María Leticia — Administradora</li>
                        </ul>
                    </div>
                    <div class="text-xs text-gray-400 text-center pt-2 border-t border-gray-100">
                        <i class="fa-solid fa-clock-rotate-left mr-1"></i> Acude con tu credencial vigente
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-notification" class="fixed bottom-5 left-5 bg-white border-l-4 border-indigo-500 shadow-2xl rounded-lg p-4 transform translate-y-20 opacity-0 transition-all duration-500 z-50 hidden flex items-center gap-4 max-w-sm">
        <div class="bg-indigo-100 text-indigo-600 p-2 rounded-full">
            <i class="fa-solid fa-bell text-xl"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-gray-800 text-sm">Actualización de Préstamo</h4>
            <p id="toast-message" class="text-sm text-gray-600 mt-0.5">...</p>
        </div>
        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <script>
        // ========================
        // ACCESIBILIDAD Y PREFERENCIAS
        // ========================

        // Cargar preferencias guardadas
        (function loadPreferences() {
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
                const btn = document.getElementById('dark-mode-toggle');
                if (btn) { btn.classList.replace('bg-gray-300', 'bg-indigo-500'); btn.querySelector('span').className = 'absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform'; btn.setAttribute('aria-checked', 'true'); }
            }
            if (localStorage.getItem('highContrast') === 'true') {
                document.documentElement.classList.add('high-contrast');
                const btn = document.getElementById('contrast-toggle');
                if (btn) { btn.classList.replace('bg-gray-300', 'bg-indigo-500'); btn.querySelector('span').className = 'absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform'; btn.setAttribute('aria-checked', 'true'); }
            }
            const fs = localStorage.getItem('fontSize') || '100';
            document.documentElement.style.setProperty('--access-font-size', fs + '%');
            const fsLabel = document.getElementById('font-size-label');
            const fsSlider = document.getElementById('font-size-slider');
            if (fsLabel) fsLabel.textContent = fs + '%';
            if (fsSlider) fsSlider.value = fs;
            if (localStorage.getItem('visualIndicators') !== 'false') {
                localStorage.setItem('visualIndicators', 'true');
            }
        })();

        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
            const btn = document.getElementById('dark-mode-toggle');
            if (isDark) {
                btn.classList.replace('bg-gray-300', 'bg-indigo-500');
                btn.querySelector('span').className = 'absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform';
            } else {
                btn.classList.replace('bg-indigo-500', 'bg-gray-300');
                btn.querySelector('span').className = 'absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform';
            }
            btn.setAttribute('aria-checked', isDark);
        }

        function toggleContrast() {
            const isHigh = document.documentElement.classList.toggle('high-contrast');
            localStorage.setItem('highContrast', isHigh);
            const btn = document.getElementById('contrast-toggle');
            if (isHigh) {
                btn.classList.replace('bg-gray-300', 'bg-indigo-500');
                btn.querySelector('span').className = 'absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform';
            } else {
                btn.classList.replace('bg-indigo-500', 'bg-gray-300');
                btn.querySelector('span').className = 'absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform';
            }
            btn.setAttribute('aria-checked', isHigh);
        }

        function setFontSize(val) {
            document.documentElement.style.setProperty('--access-font-size', val + '%');
            localStorage.setItem('fontSize', val);
            document.getElementById('font-size-label').textContent = val + '%';
        }

        function toggleVisualIndicators() {
            const enabled = localStorage.getItem('visualIndicators') !== 'false';
            const newVal = enabled ? 'false' : 'true';
            localStorage.setItem('visualIndicators', newVal);
            const btn = document.getElementById('visual-indicator-toggle');
            if (newVal === 'true') {
                btn.classList.replace('bg-gray-300', 'bg-indigo-500');
                btn.querySelector('span').className = 'absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform';
            } else {
                btn.classList.replace('bg-indigo-500', 'bg-gray-300');
                btn.querySelector('span').className = 'absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform';
            }
            btn.setAttribute('aria-checked', newVal === 'true');
        }

        // ========================
        // VISTA CUADRÍCULA / LISTA
        // ========================
        function setView(view) {
            const grid = document.getElementById('inventory-grid');
            const list = document.getElementById('inventory-list');
            const btnGrid = document.getElementById('view-grid');
            const btnList = document.getElementById('view-list');
            if (view === 'list') {
                grid.classList.add('hidden');
                list.classList.remove('hidden');
                btnGrid.className = 'px-3 py-2 rounded-lg font-bold text-sm border transition-colors bg-white text-gray-700 border-gray-300 hover:bg-gray-50';
                btnGrid.setAttribute('aria-pressed', 'false');
                btnList.className = 'px-3 py-2 rounded-lg font-bold text-sm border transition-colors bg-indigo-600 text-white border-indigo-600';
                btnList.setAttribute('aria-pressed', 'true');
            } else {
                grid.classList.remove('hidden');
                list.classList.add('hidden');
                btnGrid.className = 'px-3 py-2 rounded-lg font-bold text-sm border transition-colors bg-indigo-600 text-white border-indigo-600';
                btnGrid.setAttribute('aria-pressed', 'true');
                btnList.className = 'px-3 py-2 rounded-lg font-bold text-sm border transition-colors bg-white text-gray-700 border-gray-300 hover:bg-gray-50';
                btnList.setAttribute('aria-pressed', 'false');
            }
            localStorage.setItem('inventoryView', view);
        }

        // Cargar preferencia de vista
        (function loadViewPreference() {
            const saved = localStorage.getItem('inventoryView');
            if (saved === 'list') setView('list');
        })();

        // ========================
        // TOAST (POLLING)
        // ========================
        let lastCount = {{ auth()->user()->unreadNotifications->count() }};
        const toast = document.getElementById('toast-notification');
        const msgElement = document.getElementById('toast-message');

        function showToast(message) {
            msgElement.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.remove('translate-y-20', 'opacity-0'); }, 100);
            if (localStorage.getItem('visualIndicators') !== 'false') {
                document.body.style.transition = 'background 0.3s';
                document.body.style.background = 'rgba(99,102,241,0.15)';
                setTimeout(() => { document.body.style.background = ''; }, 500);
            }
            setTimeout(hideToast, 5000);
        }

        function hideToast() {
            toast.classList.add('translate-y-20', 'opacity-0');
            setTimeout(() => { toast.classList.add('hidden'); }, 500);
        }

        function playNotificationSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                osc.frequency.setValueAtTime(660, ctx.currentTime + 0.12);
                osc.frequency.setValueAtTime(880, ctx.currentTime + 0.24);
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
                osc.start();
                osc.stop(ctx.currentTime + 0.5);
            } catch (e) { /* fallo silencioso */ }
        }

        setInterval(() => {
            fetch('{{ route("notifications.check") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.count > lastCount) {
                        lastCount = data.count;
                        if (data.latest) {
                            showToast(data.latest.message);
                            playNotificationSound();
                        }
                        // Recargar la página suavemente para ver cambios en la tabla
                        setTimeout(() => { window.location.reload(); }, 2000);
                    }
                })
                .catch(error => console.error('Error checando notificaciones:', error));
        }, 4000);

        // --- Lógica de Modales ---
        function openModal(id, name) {
            document.getElementById('modal-item-id').value = id;
            document.getElementById('modal-title').textContent = name;
            document.getElementById('loan-modal').classList.remove('hidden');
        }

        function openProfileModal() {
            document.getElementById('profile-modal').classList.remove('hidden');
        }

        function showHelpFIM() {
            document.getElementById('help-modal').classList.remove('hidden');
        }

        function toggleQuickDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
            const btn = document.getElementById('dark-mode-toggle');
            if (btn) {
                if (isDark) {
                    btn.classList.replace('bg-gray-300', 'bg-indigo-500');
                    btn.querySelector('span').className = 'absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform';
                } else {
                    btn.classList.replace('bg-indigo-500', 'bg-gray-300');
                    btn.querySelector('span').className = 'absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform';
                }
                btn.setAttribute('aria-checked', isDark);
            }
        }
    </script>
</body>
</html>