<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-100 via-gray-50 to-indigo-100 min-h-screen">

    <nav class="bg-indigo-700 shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-white">Panel de Administrador</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-indigo-100 mr-2">Hola, Admin</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-white hover:underline border border-indigo-400 px-2 py-1 rounded">Salir</button>
                </form>
            </div>
        </div>
    </nav>

    @if(auth()->user()->unreadNotifications->count() > 0)
    <div class="max-w-6xl mx-auto mt-4"> 
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 relative shadow">
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-bold text-yellow-700">🔔 Solicitudes Recientes:</p>
                    <ul class="mt-1 space-y-1">
                        @foreach(auth()->user()->unreadNotifications as $notification)
                            <li class="text-sm text-yellow-800 flex items-center">
                                <span class="mr-2">●</span> 
                                {{ $notification->data['message'] }} ({{ $notification->data['item'] }})
                            </li>
                        @endforeach
                    </ul>
                </div>
                <a href="{{ route('notifications.read') }}" class="text-xs bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700">Limpiar lista</a>
            </div>
        </div>
    </div>
    @endif

    <div class="max-w-6xl mx-auto p-4 mt-6">
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 shadow">{{ session('success') }}</div>
        @endif

        <div class="mb-4 border-b border-gray-200">
            <nav class="flex -mb-px space-x-6">
                <button onclick="switchTab('loans')" id="tab-loans" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-indigo-600 border-indigo-500">Gestión de Préstamos</button>
                <button onclick="switchTab('inventory')" id="tab-inventory" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-700">Inventario</button>
            </nav>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-xl w-full">
            
            <div id="content-loans">
                <h2 class="text-2xl font-bold text-indigo-700 mb-6">Lista de Solicitudes</h2>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($loans as $loan)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $loan->item->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="font-bold">{{ $loan->user->username }}</div>
                                    <div class="text-xs text-gray-400">{{ $loan->user->phone }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $loan->start_date->format('d/m H:i') }} <br>
                                    <span class="text-xs">hasta {{ $loan->end_date->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full {{ $loan->status=='pending'?'bg-yellow-100 text-yellow-800':($loan->status=='active'?'bg-green-100 text-green-800':'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm flex space-x-2">
                                    @if($loan->status == 'pending')
                                        <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
                                            @csrf <input type="hidden" name="action" value="approve">
                                            <button class="text-xs bg-green-600 text-white px-2 py-1 rounded">✔ Aprobar</button>
                                        </form>
                                        <form id="form-reject-{{ $loan->id }}" action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
                                            @csrf <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="comment" id="comment-{{ $loan->id }}">
                                            <button type="button" onclick="confirmReject({{ $loan->id }})" class="text-xs bg-red-500 text-white px-2 py-1 rounded">✖ Rechazar</button>
                                        </form>
                                    @endif
                                    @if($loan->status == 'active')
                                        <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
                                            @csrf <input type="hidden" name="action" value="finish">
                                            <button class="text-xs bg-gray-500 text-white px-2 py-1 rounded">Finalizar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="content-inventory" class="hidden">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-indigo-700">Gestión de Inventario</h2>
                </div>

                <div class="bg-indigo-50 p-4 rounded-lg mb-8 border border-indigo-100">
                    <h3 class="font-bold text-indigo-800 mb-2 text-sm">Agregar Nuevo Producto</h3>
                    <form action="{{ route('admin.items.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                        @csrf
                        <div class="flex-1 w-full">
                            <label class="text-xs text-gray-600 font-bold">Nombre</label>
                            <input type="text" name="name" class="w-full p-2 border rounded text-sm" placeholder="Ej: Cable HDMI" required>
                        </div>
                        <div class="w-24">
                            <label class="text-xs text-gray-600 font-bold">Cant.</label>
                            <input type="number" name="total_count" value="1" min="1" class="w-full p-2 border rounded text-sm" required>
                        </div>
                        <div class="flex-1 w-full">
                            <label class="text-xs text-gray-600 font-bold">URL Foto</label>
                            <input type="url" name="photo_url" class="w-full p-2 border rounded text-sm" placeholder="https://...">
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded text-sm hover:bg-indigo-700">Agregar</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="p-3 text-xs font-bold text-gray-500 uppercase">Foto</th>
                                <th class="p-3 text-xs font-bold text-gray-500 uppercase">Nombre</th>
                                <th class="p-3 text-xs font-bold text-gray-500 uppercase">Stock Total</th>
                                <th class="p-3 text-xs font-bold text-gray-500 uppercase">Estado</th>
                                <th class="p-3 text-xs font-bold text-gray-500 uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3">
                                    <img src="{{ $item->photo_url ?? 'https://via.placeholder.com/50?text=No+Img' }}" 
                                         class="w-10 h-10 rounded object-cover bg-gray-200 border" 
                                         alt="Foto">
                                </td>
                                <td class="p-3 font-medium text-gray-800">{{ $item->name }}</td>
                                <td class="p-3 font-bold text-indigo-600 text-center">{{ $item->total_count }}</td>
                                <td class="p-3">
                                    @if($item->is_active)
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-bold">Activo</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-bold">Oculto</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <button onclick="openEditModal({{ $item }})" class="text-xs bg-blue-100 text-blue-600 hover:bg-blue-200 px-3 py-1 rounded font-bold">
                                        Editar
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 flex backdrop-blur-sm">
        <div class="bg-white p-6 rounded-lg shadow-2xl w-96">
            <h3 class="text-xl font-bold mb-4 text-indigo-700 border-b pb-2">Editar Producto</h3>
            
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Nombre</label>
                    <input type="text" name="name" id="edit-name" class="w-full border p-2 rounded outline-none focus:border-indigo-500" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Cantidad Total</label>
                    <input type="number" name="total_count" id="edit-count" min="0" class="w-full border p-2 rounded outline-none focus:border-indigo-500" required>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-bold mb-1 text-gray-700">URL Foto</label>
                    <input type="url" name="photo_url" id="edit-photo" class="w-full border p-2 rounded outline-none focus:border-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Asegúrate que termine en .jpg, .png o .webp</p>
                </div>

                <div class="mb-4 flex items-center bg-gray-50 p-2 rounded">
                    <input type="checkbox" name="is_active" id="edit-active" class="w-5 h-5 text-indigo-600 rounded">
                    <label for="edit-active" class="ml-2 text-sm font-bold text-gray-700">Producto Disponible (Visible)</label>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300 text-sm font-bold">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm font-bold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast-notification" class="fixed bottom-5 right-5 bg-white border-l-4 border-indigo-500 shadow-lg rounded-lg p-4 transform translate-y-20 opacity-0 transition-all duration-500 z-50 hidden flex items-center gap-3 max-w-sm">
        <div><h4 class="font-bold text-gray-800 text-sm">Notificación</h4><p id="toast-message" class="text-sm text-gray-600"></p></div>
    </div>

    <script>
        // --- Pestañas ---
        function switchTab(tab) {
            document.getElementById('content-loans').classList.add('hidden');
            document.getElementById('content-inventory').classList.add('hidden');
            document.getElementById('tab-loans').className = "whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700";
            document.getElementById('tab-inventory').className = "whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700";

            if(tab === 'loans') {
                document.getElementById('content-loans').classList.remove('hidden');
                document.getElementById('tab-loans').className = "whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-indigo-600 border-indigo-500";
            } else {
                document.getElementById('content-inventory').classList.remove('hidden');
                document.getElementById('tab-inventory').className = "whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-indigo-600 border-indigo-500";
            }
        }

        // --- Rechazo de Préstamos ---
        function confirmReject(loanId) {
            let reason = prompt("Razón del rechazo:");
            if (reason) {
                document.getElementById('comment-' + loanId).value = reason;
                document.getElementById('form-reject-' + loanId).submit();
            }
        }

        // --- Edición de Productos ---
        function openEditModal(item) {
            // Llenar el formulario con los datos del item
            document.getElementById('edit-name').value = item.name;
            document.getElementById('edit-count').value = item.total_count;
            document.getElementById('edit-photo').value = item.photo_url || '';
            document.getElementById('edit-active').checked = item.is_active == 1;
            
            // Configurar la ruta del formulario
            // Como Laravel no tiene la ruta en JS directamente, la construimos:
            let actionUrl = "{{ route('admin.items.update', ':id') }}";
            actionUrl = actionUrl.replace(':id', item.id);
            document.getElementById('edit-form').action = actionUrl;

            document.getElementById('edit-modal').classList.remove('hidden');
        }

        // --- Polling (Notificaciones) ---
        let lastCount = {{ auth()->user()->unreadNotifications->count() }};
        setInterval(() => {
            fetch('{{ route("notifications.check") }}')
                .then(res => res.json())
                .then(data => {
                    if (data.count > lastCount) {
                        lastCount = data.count;
                        if(data.latest) { 
                            document.getElementById('toast-message').textContent = data.latest.message;
                            document.getElementById('toast-notification').classList.remove('hidden', 'translate-y-20', 'opacity-0');
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    }
                });
        }, 3000);
    </script>
</body>
</html>