<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Correo - Préstamos FIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-indigo-900 via-gray-800 to-black min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md border-t-4 border-indigo-600 relative overflow-hidden">
        
        <div class="text-center mb-8">
            <div class="bg-indigo-100 text-indigo-700 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i class="fa-solid fa-envelope-circle-check text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Verifica tu Correo</h2>
            <p class="text-sm text-gray-500 mt-1">Facultad de Ingeniería Mochis</p>
        </div>

        <div class="bg-indigo-50 border-l-4 border-indigo-500 text-indigo-700 p-4 rounded mb-6 text-sm shadow-sm">
            <i class="fa-solid fa-circle-info mr-1"></i>
            Gracias por registrarte. Antes de comenzar, verifica tu correo electrónico con el enlace que te enviamos.
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded mb-6 text-sm shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>Se ha enviado un nuevo enlace de verificación a tu correo.</span>
            </div>
        @endif

        <div class="space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 transition duration-300 shadow-md flex items-center justify-center">
                    <span>Reenviar Correo de Verificación</span>
                    <i class="fa-solid fa-paper-plane ml-2"></i>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-2 px-4 border-2 border-gray-200 text-gray-600 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-300 transition duration-200 flex items-center justify-center">
                    <i class="fa-solid fa-right-from-bracket mr-2"></i>Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</body>
</html>
