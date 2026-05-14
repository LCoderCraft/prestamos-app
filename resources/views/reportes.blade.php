<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Módulo de Reportes de Préstamos</h2>
    <p class="text-gray-600 mb-6">Genera reportes de la actividad de los equipos prestados y centros de cómputo.</p>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 p-4 rounded-lg mb-6 shadow-sm border border-emerald-200 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-indigo-100 p-6 rounded-xl hover:border-indigo-500 hover:shadow-lg transition-all">
            <div class="bg-indigo-100 text-indigo-600 w-12 h-12 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-calendar-day text-xl"></i>
            </div>
            <h3 class="font-bold text-gray-800 text-lg mb-1">Corte Diario</h3>
            <p class="text-xs text-gray-500 mb-4">Préstamos completados y pendientes de hoy.</p>
            <a href="{{ route('admin.reportes.diario') }}" class="inline-block bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-indigo-700 transition text-sm shadow-sm">
                <i class="fa-solid fa-download mr-1"></i> Descargar PDF
            </a>
        </div>
        <div class="bg-white border border-indigo-100 p-6 rounded-xl hover:border-indigo-500 hover:shadow-lg transition-all">
            <div class="bg-indigo-100 text-indigo-600 w-12 h-12 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-calendar-week text-xl"></i>
            </div>
            <h3 class="font-bold text-gray-800 text-lg mb-1">Resumen Semanal</h3>
            <p class="text-xs text-gray-500 mb-4">Estadísticas de uso de los últimos 7 días.</p>
            <a href="{{ route('admin.reportes.semanal') }}" class="inline-block bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-indigo-700 transition text-sm shadow-sm">
                <i class="fa-solid fa-download mr-1"></i> Descargar PDF
            </a>
        </div>
        <div class="bg-white border border-indigo-100 p-6 rounded-xl hover:border-indigo-500 hover:shadow-lg transition-all">
            <div class="bg-indigo-100 text-indigo-600 w-12 h-12 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-chart-line text-xl"></i>
            </div>
            <h3 class="font-bold text-gray-800 text-lg mb-1">Métricas Mensuales</h3>
            <p class="text-xs text-gray-500 mb-4">Equipos más usados y usuarios frecuentes del mes.</p>
            <a href="{{ route('admin.reportes.mensual') }}" class="inline-block bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-indigo-700 transition text-sm shadow-sm">
                <i class="fa-solid fa-download mr-1"></i> Descargar PDF
            </a>
        </div>
    </div>

    <div class="mt-8 border-t pt-6">
        <h3 class="text-lg font-semibold mb-2 text-gray-800">Exportación de Datos</h3>
        <p class="text-sm text-gray-500 mb-4">Descarga el reporte completo de todos los movimientos registrados.</p>
        <div class="flex gap-3">
            <a href="{{ route('admin.reportes.diario') }}" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-red-700 transition shadow-sm flex items-center gap-2 text-sm">
                <i class="fa-solid fa-file-pdf"></i> Exportar PDF Diario
            </a>
            <a href="{{ route('admin.reportes.semanal') }}" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-red-700 transition shadow-sm flex items-center gap-2 text-sm">
                <i class="fa-solid fa-file-pdf"></i> Exportar PDF Semanal
            </a>
        </div>
    </div>
</div>
