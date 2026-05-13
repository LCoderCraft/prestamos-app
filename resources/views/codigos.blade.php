<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Gestión de Códigos de Barras</h2>
    
    <div class="bg-white p-6 rounded shadow-md w-full md:w-1/2">
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Seleccionar Equipo (Existente o Nuevo)</label>
            <select class="w-full border p-2 rounded">
                <option>Proyector Epson Modelo X</option>
                <option>Adaptador HDMI</option>
                <option>+ Registrar nuevo equipo...</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Código Asignado</label>
            <input type="text" value="PRJ-2026-001" class="w-full border p-2 rounded bg-gray-100" readonly>
        </div>

        <div class="mb-6 flex justify-center border p-4 bg-gray-50">
            <img src="https://barcode.tec-it.com/barcode.ashx?data=PRJ-2026-001&code=Code128" alt="Código de Barras" class="h-20">
        </div>

        <button class="bg-gray-800 text-white px-4 py-2 rounded w-full hover:bg-gray-900">
            Generar e Imprimir Etiqueta
        </button>
    </div>
</div>