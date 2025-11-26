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

    <div class="max-w-6xl mx-auto p-4 mt-6">
        
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 shadow">{{ session('success') }}</div>
        @endif

        <div class="mb-4 border-b border-gray-200">
            <nav class="flex -mb-px space-x-6" aria-label="Tabs">
                <button onclick="switchTab('loans')" id="tab-loans" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-indigo-600 border-indigo-500">
                    Gestión de Préstamos
                </button>
                <button onclick="switchTab('inventory')" id="tab-inventory" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300">
                    Inventario
                </button>
            </nav>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-xl w-full">
            
            <div id="content-loans">
                <h2 class="text-2xl font-bold text-indigo-700 mb-6">Lista de Solicitudes</h2>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($loans as $loan)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $loan->item->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="font-bold">{{ $loan->user->username }}</div>
                                    <div class="text-xs text-gray-400">{{ $loan->user->phone }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $loan->start_date->format('d/m H:i') }} <br>
                                    <span class="text-xs">hasta {{ $loan->end_date->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full 
                                    {{ $loan->status=='pending'?'bg-yellow-100 text-yellow-800':($loan->status=='active'?'bg-green-100 text-green-800':'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm flex space-x-2">
                                    @if($loan->status == 'pending')
                                        <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
                                            @csrf <input type="hidden" name="action" value="approve">
                                            <button class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">✔ Aprobar</button>
                                        </form>
                                        <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
                                            @csrf <input type="hidden" name="action" value="reject">
                                            <button class="text-xs bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">✖ Rechazar</button>
                                        </form>
                                    @endif
                                    @if($loan->status == 'active')
                                        <form action="{{ route('admin.loans.update', $loan->id) }}" method="POST">
                                            @csrf <input type="hidden" name="action" value="finish">
                                            <button class="text-xs bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600">Finalizar</button>
                                        </form>
                                    @endif
                                    
                                    @if($loan->user->phone)
                                        <a href="https://wa.me/{{ $loan->user->phone }}" target="_blank" class="text-xs bg-green-500 text-white px-2 py-1 rounded">WA</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="content-inventory" class="hidden">
                <h2 class="text-2xl font-bold text-indigo-700 mb-6">Agregar Producto</h2>
                <form action="{{ route('admin.items.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nombre</label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Cantidad</label>
                            <input type="number" name="total_count" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Foto URL</label>
                        <input type="url" name="photo_url" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700">Agregar</button>
                </form>

                <h3 class="mt-8 font-bold text-lg text-gray-700">Inventario Actual</h3>
                <ul class="mt-4 space-y-2">
                    @foreach($items as $item)
                        <li class="flex items-center justify-between border-b pb-2">
                            <span class="flex items-center">
                                <img src="{{ $item->photo_url }}" class="w-8 h-8 rounded mr-2 bg-gray-200">
                                {{ $item->name }}
                            </span>
                            <span class="font-bold text-indigo-600">{{ $item->total_count }} u.</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Ocultar contenidos
            document.getElementById('content-loans').classList.add('hidden');
            document.getElementById('content-inventory').classList.add('hidden');
            
            // Resetear estilos botones
            const btnLoans = document.getElementById('tab-loans');
            const btnInv = document.getElementById('tab-inventory');
            
            btnLoans.className = "whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700";
            btnInv.className = "whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700";

            // Activar selección
            if(tab === 'loans') {
                document.getElementById('content-loans').classList.remove('hidden');
                btnLoans.className = "whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-indigo-600 border-indigo-500";
            } else {
                document.getElementById('content-inventory').classList.remove('hidden');
                btnInv.className = "whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-indigo-600 border-indigo-500";
            }
        }
    </script>
</body>
</html>