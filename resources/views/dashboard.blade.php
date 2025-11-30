<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Préstamos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-100 via-gray-50 to-indigo-100 min-h-screen">
    
    <nav class="bg-indigo-700 shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-white">Catálogo de Préstamos</h1>
            <div class="flex items-center space-x-4">
                
                <button onclick="openProfileModal()" class="text-indigo-200 hover:text-white flex items-center group transition">
                    <span class="mr-2 font-bold border-b border-transparent group-hover:border-white">
                        {{ Auth::user()->username }}
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-indigo-200 hover:text-white border border-indigo-500 px-3 py-1 rounded hover:bg-indigo-600 transition">Salir</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto p-4 mt-6">
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 shadow">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 shadow">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-xl mb-6">
            <h2 class="text-lg font-semibold mb-4 text-indigo-700">Equipos Disponibles</h2>
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 flex items-center">
                            @if($item->photo_url)
                            <img class="h-10 w-10 rounded object-cover mr-3 bg-gray-200" src="{{ $item->photo_url }}"> 
                            @else
                            <div class="h-10 w-10 rounded bg-indigo-100 mr-3 flex items-center justify-center text-indigo-500">📦</div>
                            @endif
                            <span class="font-medium text-gray-900">{{ $item->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="openModal('{{ $item->id }}', '{{ $item->name }}')"
                                class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 text-sm transition shadow">
                                Solicitar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-xl">
            <h2 class="text-lg font-semibold mb-4 text-indigo-700">Mis Solicitudes</h2>
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-gray-500 text-sm">Producto</th>
                        <th class="px-6 py-3 text-gray-500 text-sm">Fecha</th>
                        <th class="px-6 py-3 text-gray-500 text-sm">Estado</th>
                        <th class="px-6 py-3 text-gray-500 text-sm">Comentario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($myLoans as $loan)
                    <tr>
                        <td class="px-6 py-4">{{ $loan->item->name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $loan->start_date->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-bold rounded 
                            {{ $loan->status === 'active' ? 'bg-green-100 text-green-800' : 
                              ($loan->status === 'rejected' ? 'bg-red-100 text-red-800' : 
                              ($loan->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 italic">{{ $loan->admin_comment ?? '--' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500 italic">No tienes préstamos activos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="loan-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 flex backdrop-blur-sm transition-opacity">
        <div class="bg-white p-6 rounded-lg shadow-2xl w-96 transform scale-100 transition-transform">
            <h3 class="text-xl font-bold mb-4 text-indigo-700 border-b pb-2" id="modal-title">Solicitar</h3>
            <form action="{{ route('loans.store') }}" method="POST">
                @csrf
                <input type="hidden" name="item_id" id="modal-item-id">
                <div class="mb-3">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Fecha</label>
                    <input type="date" name="date" class="w-full border p-2 rounded focus:ring-2 focus:ring-indigo-400 outline-none" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Hora</label>
                    <input type="time" name="time" class="w-full border p-2 rounded focus:ring-2 focus:ring-indigo-400 outline-none" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Duración (Horas)</label>
                    <input type="number" name="duration" value="1" min="1" max="5" class="w-full border p-2 rounded focus:ring-2 focus:ring-indigo-400 outline-none" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('loan-modal').classList.add('hidden')" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400 transition">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition shadow">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="profile-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50 flex backdrop-blur-sm">
        <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md relative">
            
            <button onclick="document.getElementById('profile-modal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">✖</button>

            <h3 class="text-2xl font-bold mb-6 text-indigo-700 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Configurar Perfil
            </h3>
            
            <form action="{{ route('profile.update_credentials') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" required>
                </div>

                <hr class="my-4 border-gray-200">
                <p class="text-xs text-gray-500 mb-3">Deja en blanco la nueva contraseña si no deseas cambiarla.</p>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Nueva Contraseña</label>
                    <input type="password" name="new_password" placeholder="Mínimo 8 caracteres" class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Confirmar Nueva Contraseña</label>
                    <input type="password" name="new_password_confirmation" placeholder="Repite la nueva contraseña" class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <hr class="my-4 border-gray-200">

                <div class="mb-6 bg-gray-50 p-3 rounded border border-gray-200">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Contraseña ACTUAL</label>
                    <p class="text-xs text-gray-500 mb-1">Necesaria para guardar cambios.</p>
                    <input type="password" name="current_password" placeholder="Tu contraseña actual" class="w-full border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-red-400 outline-none" required>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('profile-modal').classList.add('hidden')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition shadow font-bold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast-notification" class="fixed bottom-5 right-5 bg-white border-l-4 border-indigo-500 shadow-2xl rounded-lg p-4 transform translate-y-20 opacity-0 transition-all duration-500 z-50 hidden flex items-center gap-3 max-w-sm">
        <div class="text-indigo-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>
        <div>
            <h4 class="font-bold text-gray-800 text-sm">Nueva Notificación</h4>
            <p id="toast-message" class="text-sm text-gray-600">Mensaje aquí...</p>
        </div>
        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600 ml-auto">
            ✖
        </button>
    </div>

    <script>
        // --- 1. Lógica del Toast (Polling) ---
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
                        // Recargar página si cambia el estado del préstamo
                         setTimeout(() => { window.location.reload(); }, 1500);
                    }
                })
                .catch(error => console.error('Error checando notificaciones:', error));
        }, 3000);

        // --- 2. Lógica de Modales ---
        // Modal Préstamo
        function openModal(id, name) {
            document.getElementById('modal-item-id').value = id;
            document.getElementById('modal-title').textContent = 'Solicitar: ' + name;
            document.getElementById('loan-modal').classList.remove('hidden');
        }

        // Modal Perfil
        function openProfileModal() {
            document.getElementById('profile-modal').classList.remove('hidden');
        }
    </script>
</body>
</html>