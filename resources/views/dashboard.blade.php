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
            
            <div class="flex items-center space-x-2 md:space-x-4">
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
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fa-solid fa-boxes-stacked text-indigo-600 mr-3"></i>Equipos Disponibles
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
                            <p class="text-xs text-gray-500 mb-4">Equipo propiedad de la Facultad de Ingeniería Mochis.</p>
                        </div>
                        <button onclick="openModal('{{ $item->id }}', '{{ $item->name }}')" class="w-full bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold py-2 px-4 rounded-lg hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-colors shadow-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-hand-holding-hand"></i> Solicitar
                        </button>
                    </div>
                </div>
                @endforeach
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
                            @forelse($myLoans->where('status', '!=', 'finished') as $loan)
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
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-10 text-center">
                                    <i class="fa-solid fa-box-open text-gray-300 text-4xl mb-2"></i>
                                    <p class="text-sm text-gray-500 font-medium">No tienes solicitudes activas o en espera.</p>
                                </td>
                            </tr>
                            @endforelse
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
        <div class="bg-white p-0 rounded-xl shadow-2xl w-full max-w-md overflow-hidden relative">
            
            <div class="bg-gray-800 p-4 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold flex items-center"><i class="fa-solid fa-user-gear mr-2"></i>Configuración de Cuenta</h3>
                <button onclick="document.getElementById('profile-modal').classList.add('hidden')" class="text-gray-400 hover:text-white transition"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <form action="{{ route('profile.update_credentials') }}" method="POST" class="p-6">
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

                <div class="mb-6 bg-red-50 p-4 rounded-lg border border-red-100">
                    <label class="block text-sm font-bold mb-1 text-red-800"><i class="fa-solid fa-lock mr-1"></i>Contraseña Actual</label>
                    <p class="text-xs text-red-600 mb-2">Requerida por seguridad para guardar cualquier cambio.</p>
                    <input type="password" name="current_password" placeholder="Ingresa tu contraseña actual" class="w-full border border-red-300 p-2.5 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm" required>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('profile-modal').classList.add('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 shadow-md transition">Guardar Perfil</button>
                </div>
            </form>
        </div>
    </div>

    <div class="fixed bottom-4 right-4 z-40">
        <button onclick="alert('Centro de Soporte FIM.\nSi tienes problemas con tu solicitud, acude físicamente al centro de cómputo con tu credencial.')" class="bg-gray-800 text-white p-3 rounded-full shadow-lg hover:bg-gray-700 hover:shadow-xl transition-all duration-300 flex items-center group">
            <i class="fa-solid fa-circle-info text-xl"></i>
            <span class="max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-300 ease-in-out whitespace-nowrap group-hover:ml-2 font-medium">
                Ayuda FIM
            </span>
        </button>
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
        // --- Lógica del Toast (Polling) ---
        let lastCount = {{ auth()->user()->unreadNotifications->count() }};
        const toast = document.getElementById('toast-notification');
        const msgElement = document.getElementById('toast-message');

        function showToast(message) {
            msgElement.textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => { toast.classList.remove('translate-y-20', 'opacity-0'); }, 100);
            setTimeout(hideToast, 5000);
        }

        function hideToast() {
            toast.classList.add('translate-y-20', 'opacity-0');
            setTimeout(() => { toast.classList.add('hidden'); }, 500);
        }

        setInterval(() => {
            fetch('{{ route("notifications.check") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.count > lastCount) {
                        lastCount = data.count;
                        if (data.latest) {
                            showToast(data.latest.message);
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
    </script>
</body>
</html>