<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centros de Cómputo - FIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }
        .tg-event { position: absolute; inset: 2px; border-radius: 4px; padding: 2px 5px; font-size: 10px; font-weight: 500; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; cursor: pointer; }
        .ev-group { background: #dbeafe; color: #1e40af; border-left: 3px solid #3b82f6; }
        .ev-teacher { background: #d1fae5; color: #065f46; border-left: 3px solid #10b981; }
        .ev-user { background: #ede9fe; color: #4c1d95; border-left: 3px solid #8b5cf6; }
        .ev-pending { background: #fef3c7; color: #92400e; border-left: 3px dashed #f59e0b; }
        :root { --access-font-size: 100%; }
        body { font-size: var(--access-font-size); }
        .dark body, .dark .bg-gradient-to-br, .dark .bg-gray-50, .dark .bg-gray-100, .dark .bg-gray-200 { background: #0a1628 !important; }
        .dark .bg-white { background: #0f1d35 !important; }
        .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-600, .dark .text-gray-500,
        .dark .text-gray-400, .dark .text-gray-300, .dark .text-indigo-600, .dark .text-indigo-700 { color: #c8d6e5 !important; }
        .dark .border-gray-100, .dark .border-gray-200 { border-color: #1a3356 !important; }
        .dark .bg-indigo-800 { background: #0a1e3d !important; }
        .dark .hover\:bg-gray-50:hover { background-color: #0d1f3c !important; }
        .dark .shadow-sm, .dark .shadow-md, .dark .shadow-lg, .dark .shadow-xl { box-shadow: 0 1px 3px 0 rgba(0,0,0,0.4) !important; }
        .dark .text-indigo-200, .dark .text-indigo-300 { color: #5a8ec9 !important; }
        .dark .text-indigo-100 { color: #8ab4f0 !important; }
        .dark .text-indigo-800 { color: #8ab4f0 !important; }
        .dark .text-emerald-500, .dark .text-emerald-600, .dark .text-emerald-700 { color: #5cd68a !important; }
        .dark .text-red-500, .dark .text-red-600, .dark .text-red-700 { color: #f87171 !important; }
        .dark .text-amber-500, .dark .text-amber-600 { color: #e8c84a !important; }
        .dark .bg-indigo-50 { background: #0f2847 !important; }
        .dark .bg-emerald-50 { background: #0a2a1a !important; }
        .dark .bg-amber-50 { background: #2a2a0a !important; }
        .dark .bg-red-50 { background: #2a0a0a !important; }
        .dark select, .dark input[type="text"], .dark input[type="date"], .dark input[type="time"], .dark textarea { background-color: #0d1f3c !important; border-color: #1a3356 !important; color: #c8d6e5 !important; }
        .dark option { background-color: #0f1d35 !important; color: #c8d6e5 !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 min-h-screen font-sans">
    <nav class="bg-indigo-800 shadow-lg border-b-4 border-indigo-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-white text-indigo-800 p-2 rounded-lg shadow-sm">
                    <i class="fa-solid fa-computer text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-wide">Centros de Cómputo</h1>
                    <p class="text-xs text-indigo-200 hidden md:block">Reservaciones FIM</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('dashboard') }}" class="text-indigo-200 hover:text-white text-sm flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
                <button onclick="toggleRoomsDarkMode()" class="text-indigo-300 hover:text-white text-sm px-2 py-1 rounded hover:bg-indigo-700 transition" title="Modo oscuro">
                    <i class="fa-solid fa-moon"></i>
                </button>
            </div>
        </div>
    </nav>
    <div class="max-w-7xl mx-auto p-4 mt-4 animate-fadeIn">
        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-lg mb-6 shadow-sm border border-emerald-200 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 shadow-sm border border-red-200 flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-red-500 text-lg"></i>
                <span>{!! session('error') !!}</span>
            </div>
        @endif
        <!-- Navegación de salas -->
        <div class="flex gap-2 mb-4 flex-wrap">
            @foreach($rooms as $room)
                <a href="{{ route('rooms.index', ['room_id' => $room->id, 'week' => request('week')]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition-all border
                   {{ $room->id == $selectedRoomId ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-200 hover:border-indigo-300' }}">
                    {{ $room->name }} ({{ $room->capacity }} eq.)
                </a>
            @endforeach
        </div>
        <!-- Cabecera semana -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-3">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fa-solid fa-calendar-week text-indigo-600 mr-2"></i>
                Semana del {{ $startOfWeek->format('d/m') }} al {{ $endOfWeek->format('d/m/Y') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <div class="flex bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                    <button onclick="setCalendarView('full')" id="view-full" class="px-3 py-1.5 text-xs font-bold bg-indigo-600 text-white transition">Día Completo</button>
                    <button onclick="setCalendarView('morning')" id="view-morning" class="px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">Mañana</button>
                    <button onclick="setCalendarView('afternoon')" id="view-afternoon" class="px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">Tarde</button>
                </div>
                <a href="{{ route('rooms.index', ['room_id' => $selectedRoomId, 'week' => $startOfWeek->copy()->subWeek()->format('Y-m-d')]) }}"
                   class="bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-50 transition flex items-center gap-1">
                    <i class="fa-solid fa-chevron-left"></i> Anterior
                </a>
                <a href="{{ route('rooms.index', ['room_id' => $selectedRoomId, 'week' => now()->format('Y-m-d')]) }}"
                   class="bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-50 transition flex items-center gap-1">
                    Hoy
                </a>
                <a href="{{ route('rooms.index', ['room_id' => $selectedRoomId, 'week' => $startOfWeek->copy()->addWeek()->format('Y-m-d')]) }}"
                   class="bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-50 transition flex items-center gap-1">
                    Siguiente <i class="fa-solid fa-chevron-right"></i>
                </a>
            </div>
        </div>
        <!-- Grid calendario -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="w-16 p-2 text-left text-gray-500 font-medium text-xs"></th>
                            @foreach($weekDays as $day)
                                <th class="p-2 text-center {{ $day->isToday() ? 'bg-indigo-50' : '' }}">
                                    <div class="text-xs text-gray-500 font-medium">{{ $day->locale('es')->isoFormat('ddd') }}</div>
                                    <div class="text-lg font-bold {{ $day->isToday() ? 'text-indigo-600' : 'text-gray-800' }}">{{ $day->format('d') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="calendar-body">
                        @foreach($hours as $hour)
                            <tr data-hour="{{ $hour }}">
                                <td class="p-2 text-xs text-gray-400 font-medium text-center w-16">{{ sprintf('%02d:00', $hour) }}</td>
                                @foreach($weekDays as $day)
                                    @php
                                        $cellStart = $day->copy()->setHour($hour)->setMinute(0);
                                        $cellEnd = $cellStart->copy()->addHour();
                                        $events = $reservations->filter(function ($r) use ($cellStart, $cellEnd) {
                                            return $r->start_date < $cellEnd && $r->end_date > $cellStart;
                                        });
                                    @endphp
                                    <td class="p-0.5 relative border-l border-gray-50 h-10 {{ $day->isToday() ? 'bg-indigo-50/30' : '' }}">
                                        @foreach($events as $event)
                                            @php
                                                $typeClass = $event->requester_type === 'group' ? 'ev-group' : ($event->requester_type === 'teacher' ? 'ev-teacher' : 'ev-user');
                                                $label = $event->requester_type === 'group' ? 'Grupo ' . $event->group_name
                                                    : ($event->requester_type === 'teacher' ? $event->teacher_name
                                                    : $event->user->username . ' (Alumno)');
                                                if ($event->status === 'pending') $typeClass = 'ev-pending';
                                            @endphp
                                            <div class="tg-event {{ $typeClass }}" title="{{ $label }}: {{ $event->purpose }}">
                                                {{ $label }}
                                            </div>
                                        @endforeach
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Leyenda -->
        <div class="flex gap-4 mt-4 flex-wrap">
            <span class="flex items-center gap-1.5 text-xs text-gray-600"><span class="w-3 h-3 rounded bg-blue-100 border-l-2 border-blue-500 inline-block"></span> Grupo escolar</span>
            <span class="flex items-center gap-1.5 text-xs text-gray-600"><span class="w-3 h-3 rounded bg-green-100 border-l-2 border-green-500 inline-block"></span> Profesor</span>
            <span class="flex items-center gap-1.5 text-xs text-gray-600"><span class="w-3 h-3 rounded bg-purple-100 border-l-2 border-purple-500 inline-block"></span> Alumno</span>
            <span class="flex items-center gap-1.5 text-xs text-gray-600"><span class="w-3 h-3 rounded bg-amber-100 border-l-2 border-dashed border-amber-500 inline-block"></span> Pendiente</span>
        </div>
        <!-- Botón nueva reserva -->
        <div class="mt-6 flex justify-center gap-3">
            <button onclick="document.getElementById('reserve-modal').classList.remove('hidden')"
                    class="bg-gradient-to-r from-indigo-600 to-indigo-500 text-white px-8 py-3.5 rounded-xl font-bold hover:from-indigo-700 hover:to-indigo-600 shadow-lg hover:shadow-xl transition-all duration-300 inline-flex items-center gap-3 text-lg transform hover:scale-[1.02] active:scale-[0.98]">
                <span class="bg-white/20 p-1.5 rounded-lg"><i class="fa-solid fa-plus"></i></span>
                Nueva Reservación
            </button>
        </div>
    </div>
    <!-- Modal reserva -->
    <div id="reserve-modal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="bg-indigo-600 p-4 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold flex items-center"><i class="fa-solid fa-calendar-plus mr-2"></i>Nueva Reservación</h3>
                <button onclick="document.getElementById('reserve-modal').classList.add('hidden')" class="text-indigo-200 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <form action="{{ route('rooms.store') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2 text-gray-700">Tipo de solicitante</label>
                    <div class="flex gap-2" x-data="{ type: 'user' }">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="requester_type" value="user" class="hidden peer" checked onchange="toggleRequesterFields()">
                            <div class="p-3 border rounded-lg text-center text-sm peer-checked:bg-indigo-50 peer-checked:border-indigo-500 peer-checked:text-indigo-700 border-gray-200 hover:bg-gray-50 transition font-medium">
                                <i class="fa-solid fa-user block text-lg mb-1"></i> Alumno
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="requester_type" value="group" class="hidden peer" onchange="toggleRequesterFields()">
                            <div class="p-3 border rounded-lg text-center text-sm peer-checked:bg-indigo-50 peer-checked:border-indigo-500 peer-checked:text-indigo-700 border-gray-200 hover:bg-gray-50 transition font-medium">
                                <i class="fa-solid fa-users block text-lg mb-1"></i> Grupo
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="requester_type" value="teacher" class="hidden peer" onchange="toggleRequesterFields()">
                            <div class="p-3 border rounded-lg text-center text-sm peer-checked:bg-indigo-50 peer-checked:border-indigo-500 peer-checked:text-indigo-700 border-gray-200 hover:bg-gray-50 transition font-medium">
                                <i class="fa-solid fa-chalkboard-user block text-lg mb-1"></i> Profesor
                            </div>
                        </label>
                    </div>
                </div>
                <div id="field-group" class="mb-3 hidden">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Grupo (ej. 3-01)</label>
                    <input type="text" name="group_name" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" placeholder="2-01, 3-02...">
                </div>
                <div id="field-teacher" class="mb-3 hidden">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Nombre del profesor/investigador</label>
                    <input type="text" name="teacher_name" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" placeholder="Dr. Juan Medina">
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-700">Centro de cómputo</label>
                        <select name="computer_room_id" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ $room->id == $selectedRoomId ? 'selected' : '' }}>{{ $room->name }} ({{ $room->capacity }} eq.)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-700">Motivo de uso</label>
                        <select name="purpose" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            <option>Clase / práctica</option>
                            <option>Proyecto tesis</option>
                            <option>Investigación</option>
                            <option>Examen</option>
                            <option>Otro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-700">Fecha</label>
                        <input type="date" name="date" min="{{ date('Y-m-d') }}" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-700">Hora inicio</label>
                        <input type="time" name="time" value="07:00" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-700">Duración</label>
                        <select name="duration" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            <option value="1">1 hora</option>
                            <option value="2">2 horas</option>
                            <option value="3">3 horas</option>
                            <option value="4">4 horas</option>
                            <option value="5">Turno completo (5h)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-700">Equipos requeridos</label>
                        <input type="number" name="computers_needed" value="0" min="0" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" placeholder="0 = todos">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('reserve-modal').classList.add('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 shadow-md">Confirmar Reservación</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function toggleRequesterFields() {
            const type = document.querySelector('input[name="requester_type"]:checked').value;
            document.getElementById('field-group').classList.toggle('hidden', type !== 'group');
            document.getElementById('field-teacher').classList.toggle('hidden', type !== 'teacher');
        }
        (function() {
            if (localStorage.getItem('roomsDarkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
        function toggleRoomsDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('roomsDarkMode', isDark);
        }
        function setCalendarView(view) {
            const rows = document.querySelectorAll('#calendar-body tr');
            const btns = ['full', 'morning', 'afternoon'];
            btns.forEach(v => {
                const btn = document.getElementById('view-' + v);
                if (v === view) {
                    btn.className = 'px-3 py-1.5 text-xs font-bold bg-indigo-600 text-white transition';
                } else {
                    btn.className = 'px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition';
                }
            });
            rows.forEach(row => {
                const hour = parseInt(row.dataset.hour);
                if (view === 'full') {
                    row.style.display = '';
                } else if (view === 'morning') {
                    row.style.display = (hour >= 7 && hour <= 13) ? '' : 'none';
                } else if (view === 'afternoon') {
                    row.style.display = (hour >= 14 && hour <= 20) ? '' : 'none';
                }
            });
        }
    </script>
</body>
</html>