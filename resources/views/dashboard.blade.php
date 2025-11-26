<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Préstamo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-100 via-gray-50 to-indigo-100 min-h-screen">

    <nav class="bg-indigo-700 shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-white">Catálogo de Préstamos</h1>
            <div class="flex items-center space-x-4">
                <span class="text-indigo-200 flex items-center">
                    <span class="mr-2">{{ Auth::user()->username }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-indigo-200 hover:text-white border border-indigo-400 px-3 py-1 rounded">Salir</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto p-4 mt-6">
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-xl mb-6">
            <h2 class="text-lg font-semibold mb-4 text-indigo-700">Equipos Disponibles</h2>
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($items as $item)
                    <tr>
                        <td class="px-6 py-4 flex items-center">
                            @if($item->photo_url)
                                <img class="h-10 w-10 rounded object-cover mr-3" src="{{ $item->photo_url }}"> 
                            @else
                                <div class="h-10 w-10 rounded bg-indigo-200 mr-3"></div>
                            @endif
                            <span class="font-medium text-gray-900">{{ $item->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="openModal('{{ $item->id }}', '{{ $item->name }}')" 
                                class="bg-indigo-600 text-white font-bold py-2 px-3 rounded hover:bg-indigo-700 text-sm transition">
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
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comentarios</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($myLoans as $loan)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $loan->item->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $loan->start_date->format('d/m H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-bold rounded-full 
                            {{ $loan->status == 'active' ? 'bg-green-100 text-green-800' : 
                              ($loan->status == 'rejected' ? 'bg-red-100 text-red-800' : 
                              ($loan->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 italic">{{ $loan->admin_comment ?? '--' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No tienes préstamos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="loan-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white top-0">
            <h3 class="text-xl font-bold mb-4 text-indigo-700" id="modal-title">Solicitar Préstamo</h3>
            
            <form action="{{ route('loans.store') }}" method="POST">
                @csrf
                <input type="hidden" name="item_id" id="modal-item-id">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Fecha de Uso</label>
                    <input type="date" name="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Hora de Inicio</label>
                    <input type="time" name="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Duración (Horas)</label>
                    <input type="number" name="duration" min="1" max="5" value="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="document.getElementById('loan-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cerrar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Confirmar Solicitud</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, name) {
            document.getElementById('modal-item-id').value = id;
            document.getElementById('modal-title').textContent = 'Solicitar: ' + name;
            document.getElementById('loan-modal').classList.remove('hidden');
            document.getElementById('loan-modal').classList.add('flex');
        }
    </script>
</body>
</html>