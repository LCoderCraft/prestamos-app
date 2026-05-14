<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Centros de Cómputo - FIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --access-font-size: 100%; }
        body { font-size: var(--access-font-size); }
        .dark body, .dark .bg-gradient-to-br, .dark .bg-gray-50, .dark .bg-gray-100, .dark .bg-gray-200 { background: #1a1a2e !important; }
        .dark .bg-white { background: #16213e !important; }
        .dark .bg-gray-50 { background: #1a1a2e !important; }
        .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-600, .dark .text-gray-500,
        .dark .text-gray-400, .dark .text-indigo-600, .dark .text-indigo-700 { color: #e0e0e0 !important; }
        .dark .border-gray-100, .dark .border-gray-200, .dark .border-gray-300 { border-color: #2a2a4a !important; }
        .dark .divide-gray-100 > * { border-color: #2a2a4a !important; }
        .dark .bg-indigo-50 { background: #1a1a3e !important; }
        .dark .bg-emerald-50 { background: #0a2a1a !important; }
        .dark .bg-amber-50 { background: #2a2a0a !important; }
        .dark .bg-red-50 { background: #2a0a0a !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 min-h-screen font-sans">
    <nav class="bg-indigo-800 shadow-lg border-b-4 border-indigo-500">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-white text-indigo-800 p-2 rounded-lg shadow-sm">
                    <i class="fa-solid fa-computer text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-wide">Administración de Centros</h1>
                    <p class="text-xs text-indigo-200">Gestión de reservaciones FIM</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="toggleRoomsAdminDarkMode()" class="text-indigo-300 hover:text-white text-sm px-2 py-1 rounded hover:bg-indigo-700 transition" title="Modo oscuro">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <a href="{{ route('admin.dashboard') }}" class="text-indigo-200 hover:text-white text-sm"><i class="fa-solid fa-arrow-left"></i> Volver</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="text-sm text-red-300 hover:text-red-100 font-semibold">Salir <i class="fa-solid fa-right-from-bracket"></i></button>
                </form>
            </div>
        </div>
    </nav>
    <div class="max-w-7xl mx-auto p-4 mt-4">
        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-lg mb-6 shadow-sm border border-emerald-200 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-lg"><i class="fa-solid fa-clock-rotate-left text-xl"></i></div>
                <div><p class="text-sm text-gray-500 font-semibold">Pendientes</p><p class="text-2xl font-bold text-gray-800">{{ $reservations->where('status', 'pending')->count() }}</p></div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="bg-emerald-100 text-emerald-600 p-3 rounded-lg"><i class="fa-solid fa-circle-play text-xl"></i></div>
                <div><p class="text-sm text-gray-500 font-semibold">Activos</p><p class="text-2xl font-bold text-gray-800">{{ $reservations->where('status', 'active')->count() }}</p></div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="bg-indigo-100 text-indigo-600 p-3 rounded-lg"><i class="fa-solid fa-building text-xl"></i></div>
                <div><p class="text-sm text-gray-500 font-semibold">Centros</p><p class="text-2xl font-bold text-gray-800">{{ $rooms->count() }}</p></div>
            </div>
        </div>

        <!-- CENTROS DE CÓMPUTO -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 flex items-center"><i class="fa-solid fa-building text-indigo-600 mr-2"></i>Centros de Cómputo</h2>
                <button onclick="document.getElementById('room-modal').classList.remove('hidden')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 transition"><i class="fa-solid fa-plus"></i> Agregar Centro</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4">
                @forelse($rooms as $room)
                    <div class="border border-gray-200 rounded-xl p-4 {{ $room->is_active ? 'bg-white' : 'bg-gray-50 opacity-75' }}">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-indigo-100 text-indigo-600 p-2.5 rounded-lg">
                                <i class="fa-solid fa-display text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-800 text-sm truncate">{{ $room->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $room->capacity }} equipos</p>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $room->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $room->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mb-3">{{ $room->location ?? 'Sin ubicación' }}</p>
                        <div class="flex gap-2">
                            <button onclick="openEditRoomModal({{ $room->id }}, '{{ addslashes($room->name) }}', {{ $room->capacity }}, '{{ addslashes($room->location ?? '') }}', {{ $room->is_active ? 'true' : 'false' }})"
                                class="flex-1 bg-gray-100 text-gray-700 text-xs font-bold py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 transition border border-gray-200">
                                <i class="fa-solid fa-pen mr-1"></i> Editar
                            </button>
                            <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este centro?')">
                                @csrf @method('DELETE')
                                <button class="bg-red-50 text-red-600 text-xs font-bold py-2 px-3 rounded-lg hover:bg-red-100 transition border border-red-200">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8 text-gray-400">
                        <i class="fa-solid fa-building text-4xl mb-2"></i>
                        <p>No hay centros de cómputo registrados</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Reservaciones activas/pendientes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800 flex items-center"><i class="fa-solid fa-list-check text-indigo-600 mr-2"></i>Reservaciones Pendientes y Activas</h2>
                <button onclick="document.getElementById('room-modal').classList.remove('hidden')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 transition"><i class="fa-solid fa-plus"></i> Agregar Centro</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-4 py-3 text-gray-500 text-xs uppercase font-bold">Solicitante</th>
                        <th class="px-4 py-3 text-gray-500 text-xs uppercase font-bold">Tipo</th>
                        <th class="px-4 py-3 text-gray-500 text-xs uppercase font-bold">Centro</th>
                        <th class="px-4 py-3 text-gray-500 text-xs uppercase font-bold">Horario</th>
                        <th class="px-4 py-3 text-gray-500 text-xs uppercase font-bold">Estado</th>
                        <th class="px-4 py-3 text-gray-500 text-xs uppercase font-bold">Acción</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($reservations as $res)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium">{{ $res->requester_type === 'group' ? 'Grupo '.$res->group_name : ($res->requester_type === 'teacher' ? $res->teacher_name : $res->user->username) }}</div>
                                <div class="text-xs text-gray-400">{{ $res->purpose }}</div>
                            </td>
                            <td class="px-4 py-3"><span class="text-xs px-2 py-1 rounded-full {{ $res->requester_type === 'group' ? 'bg-blue-100 text-blue-700' : ($res->requester_type === 'teacher' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700') }}">{{ $res->requester_type === 'group' ? 'Grupo' : ($res->requester_type === 'teacher' ? 'Profesor' : 'Alumno') }}</span></td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $res->computerRoom->name }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                <div>{{ $res->start_date->format('d/m/Y') }}</div>
                                <div>{{ $res->start_date->format('H:i') }} - {{ $res->end_date->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $res->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $res->status === 'active' ? 'Activo' : 'Pendiente' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button onclick="openActionModal('{{ $res->id }}', '{{ addslashes($res->user->username) }}', '{{ $res->computerRoom->name }}')" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Gestionar</button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Sin reservaciones pendientes</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Historial -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-800 flex items-center"><i class="fa-solid fa-clock-rotate-left text-gray-500 mr-2"></i>Historial</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50"><tr>
                        <th class="px-4 py-3 text-gray-400 text-xs uppercase font-bold">Solicitante</th>
                        <th class="px-4 py-3 text-gray-400 text-xs uppercase font-bold">Centro</th>
                        <th class="px-4 py-3 text-gray-400 text-xs uppercase font-bold">Fecha</th>
                        <th class="px-4 py-3 text-gray-400 text-xs uppercase font-bold">Estado</th>
                        <th class="px-4 py-3 text-gray-400 text-xs uppercase font-bold">Observación</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($history as $res)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $res->requester_type === 'group' ? 'Grupo '.$res->group_name : ($res->requester_type === 'teacher' ? $res->teacher_name : $res->user->username) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $res->computerRoom->name }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $res->start_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-bold rounded-full {{ $res->status === 'finished' ? 'bg-gray-100 text-gray-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $res->status === 'finished' ? 'Finalizado' : 'Rechazado' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 italic">{{ $res->admin_comment ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">Sin historial</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal acción -->
    <div id="action-modal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-indigo-600 p-4 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold" id="action-title">Gestionar Reservación</h3>
                <button onclick="document.getElementById('action-modal').classList.add('hidden')" class="text-indigo-200 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <form id="action-form" method="POST" class="p-6">
                @csrf
                <div class="mb-4 bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm"><strong>Solicitante:</strong> <span id="action-user"></span></p>
                    <p class="text-sm"><strong>Centro:</strong> <span id="action-room"></span></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Acción</label>
                    <select name="action" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <option value="approve"><i class="fa-solid fa-check"></i> Aprobar</option>
                        <option value="reject">Rechazar</option>
                        <option value="finish">Marcar como finalizado</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Comentario / Observación</label>
                    <textarea name="comment" rows="3" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" placeholder="Opcional..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('action-modal').classList.add('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal agregar centro -->
    <div id="room-modal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-indigo-600 p-4 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold">Nuevo Centro de Cómputo</h3>
                <button onclick="document.getElementById('room-modal').classList.add('hidden')" class="text-indigo-200 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <form action="{{ route('admin.rooms.store') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Nombre del centro</label>
                    <input type="text" name="name" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" placeholder="Centro A" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Capacidad (equipos)</label>
                    <input type="number" name="capacity" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Ubicación</label>
                    <input type="text" name="location" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" placeholder="Edificio A, 2do piso">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('room-modal').classList.add('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal editar centro -->
    <div id="edit-room-modal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-gray-800 p-4 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold"><i class="fa-solid fa-pen-to-square mr-2"></i>Editar Centro de Cómputo</h3>
                <button onclick="document.getElementById('edit-room-modal').classList.add('hidden')" class="text-gray-400 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <form id="edit-room-form" method="POST" class="p-6">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Nombre del centro</label>
                    <input type="text" name="name" id="edit-room-name" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Capacidad (equipos)</label>
                    <input type="number" name="capacity" id="edit-room-capacity" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Ubicación</label>
                    <input type="text" name="location" id="edit-room-location" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                </div>
                <div class="mb-6 bg-gray-50 p-3 rounded-lg border border-gray-200">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" id="edit-room-active" class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                        <span class="ml-3 text-sm font-bold text-gray-700">Centro activo y visible</span>
                    </label>
                    <p class="ml-8 mt-1 text-xs text-gray-500">Desmárcalo para deshabilitar reservaciones en este centro.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('edit-room-modal').classList.add('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast-notification" class="fixed bottom-5 right-5 bg-white border-l-4 border-amber-500 shadow-2xl rounded-lg p-4 transform translate-y-20 opacity-0 transition-all duration-500 z-50 hidden flex items-center gap-4 max-w-sm">
        <div class="bg-amber-100 text-amber-500 p-2 rounded-full">
            <i class="fa-solid fa-bell text-xl"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-gray-800 text-sm">Notificación</h4>
            <p id="toast-message" class="text-sm text-gray-600 mt-0.5">...</p>
        </div>
        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <script>
        (function() {
            if (localStorage.getItem('adminRoomsDarkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
        function toggleRoomsAdminDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('adminRoomsDarkMode', isDark);
        }

        function openActionModal(id, user, room) {
            document.getElementById('action-user').textContent = user;
            document.getElementById('action-room').textContent = room;
            document.getElementById('action-form').action = '{{ url("admin/rooms") }}/' + id + '/status';
            document.getElementById('action-modal').classList.remove('hidden');
        }

        function openEditRoomModal(id, name, capacity, location, isActive) {
            document.getElementById('edit-room-name').value = name;
            document.getElementById('edit-room-capacity').value = capacity;
            document.getElementById('edit-room-location').value = location;
            document.getElementById('edit-room-active').checked = isActive;
            document.getElementById('edit-room-form').action = '{{ url("admin/rooms") }}/' + id;
            document.getElementById('edit-room-modal').classList.remove('hidden');
        }

        // Notificaciones con sonido
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
            } catch (e) {}
        }

        let lastNotifCount = {{ auth()->user()->unreadNotifications->count() ?? 0 }};
        const toastNotif = document.getElementById('toast-notification');
        const toastMsg = document.getElementById('toast-message');

        function showToast(msg) {
            toastMsg.textContent = msg;
            toastNotif.classList.remove('hidden');
            setTimeout(() => { toastNotif.classList.remove('translate-y-20', 'opacity-0'); }, 100);
            setTimeout(hideToast, 8000);
        }
        function hideToast() {
            toastNotif.classList.add('translate-y-20', 'opacity-0');
            setTimeout(() => { toastNotif.classList.add('hidden'); }, 500);
        }

        setInterval(() => {
            fetch('{{ route("notifications.check") }}')
                .then(r => r.json())
                .then(d => {
                    if (d.count > lastNotifCount) {
                        lastNotifCount = d.count;
                        if (d.latest) { showToast(d.latest.message); playNotificationSound(); }
                        setTimeout(() => window.location.reload(), 2500);
                    }
                })
                .catch(() => {});
        }, 4000);
    </script>
</body>
</html>