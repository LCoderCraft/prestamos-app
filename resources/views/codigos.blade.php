<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Gestión de Códigos de Barras</h2>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 p-4 rounded-lg mb-6 shadow-sm border border-emerald-200 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 w-full md:w-1/2">
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Seleccionar Equipo</label>
            <select id="item-select" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none" onchange="updatePreview()">
                <option value="">-- Selecciona un equipo --</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" data-barcode="{{ $item->barcode ?? '' }}" data-name="{{ $item->name }}">
                        {{ $item->name }} {{ $item->barcode ? '(Código: '.$item->barcode.')' : '(Sin código)' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Código Asignado</label>
            <div class="relative">
                <i class="fa-solid fa-hashtag absolute left-3 top-3 text-gray-400"></i>
                <input type="text" id="barcode-display" value="" class="w-full pl-9 border p-2.5 rounded-lg bg-gray-100 text-gray-600 font-mono font-bold" readonly>
            </div>
        </div>

        <div class="mb-4 flex justify-center border p-6 bg-gray-50 rounded-lg min-h-[120px] items-center" id="preview-container">
            <div id="preview-empty" class="text-gray-400 text-sm italic">
                <i class="fa-solid fa-barcode text-4xl block mb-2 text-gray-300"></i>
                Selecciona un equipo para ver su código
            </div>
            <div id="preview-content" class="hidden flex flex-col items-center">
                <img id="barcode-img" src="" alt="Código de Barras" class="h-16 mix-blend-multiply">
                <p id="barcode-label" class="text-xs font-mono mt-1 text-gray-600"></p>
            </div>
        </div>

        <div class="flex gap-3">
            <button id="btn-print" onclick="printBarcode()" class="bg-gray-800 text-white px-4 py-2.5 rounded-lg w-full hover:bg-gray-900 font-bold flex items-center justify-center gap-2 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <i class="fa-solid fa-print"></i> Imprimir Etiqueta
            </button>
            <button id="btn-generate" onclick="generateBarcode()" class="bg-indigo-600 text-white px-4 py-2.5 rounded-lg hover:bg-indigo-700 font-bold flex items-center justify-center gap-2 transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <i class="fa-solid fa-arrows-rotate"></i> Generar Código
            </button>
        </div>
    </div>

    <div class="mt-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Todos los Equipos</h3>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Equipo</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Código de Barras</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $item->name }}</td>
                        <td class="px-4 py-3">
                            @if($item->barcode)
                                <span class="font-mono text-indigo-600 font-bold">{{ $item->barcode }}</span>
                            @else
                                <span class="text-red-400 italic text-xs">Sin código asignado</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $item->total_count }} unid.</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-bold rounded-full {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $item->is_active ? 'Disponible' : 'Inactivo' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let selectedItemId = null;
        let selectedBarcode = '';

        function updatePreview() {
            const select = document.getElementById('item-select');
            const option = select.options[select.selectedIndex];
            const barcode = option.dataset.barcode;
            const name = option.dataset.name;
            selectedItemId = option.value;
            selectedBarcode = barcode;

            const previewEmpty = document.getElementById('preview-empty');
            const previewContent = document.getElementById('preview-content');
            const barcodeImg = document.getElementById('barcode-img');
            const barcodeDisplay = document.getElementById('barcode-display');
            const barcodeLabel = document.getElementById('barcode-label');
            const btnPrint = document.getElementById('btn-print');
            const btnGenerate = document.getElementById('btn-generate');

            if (!selectedItemId || !barcode) {
                previewEmpty.classList.remove('hidden');
                previewContent.classList.add('hidden');
                barcodeDisplay.value = barcode || '';
                btnPrint.disabled = true;
                btnGenerate.disabled = !selectedItemId;
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

        function printBarcode() {
            if (!selectedItemId || !selectedBarcode) return;
            const win = window.open('', '_blank');
            win.document.write(`
                <html><head><title>Etiqueta - Código de Barras</title>
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
                        <img src="https://barcode.tec-it.com/barcode.ashx?data=${selectedBarcode}&code=Code128" alt="Código">
                        <p>${selectedBarcode}</p>
                    </div>
                    <script>window.print();window.close();<\/script>
                </body></html>
            `);
            win.document.close();
        }

        function generateBarcode() {
            if (!selectedItemId) return;
            window.location.href = '/admin/codigos/' + selectedItemId + '/regenerar';
        }
    </script>
</div>
