<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Codigo - Prestamos FIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-indigo-900 via-gray-800 to-black min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md border-t-4 border-indigo-600 relative overflow-hidden">
        
        <div class="text-center mb-8">
            <div class="bg-indigo-100 text-indigo-700 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i class="fa-solid fa-shield-halved text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Verificar Codigo</h2>
            <p class="text-sm text-gray-500 mt-1">Ingresa el codigo de 6 digitos enviado a tu correo</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded mb-6 text-sm shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 text-sm shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 text-sm shadow-sm">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2"><i class="fa-solid fa-circle-exclamation"></i>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.verify.code.submit') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email ?? request('email') }}">
            
            <div class="mb-4">
                <label for="email_display" class="block text-gray-700 text-sm font-semibold mb-2">Cuenta</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-envelope text-gray-400"></i>
                    </div>
                    <input type="text" id="email_display" value="{{ $email ?? request('email') }}" class="w-full pl-10 px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-600 text-sm" readonly>
                </div>
            </div>
            
            <div class="mb-6">
                <label for="code" class="block text-gray-700 text-sm font-semibold mb-2">Codigo de Verificacion</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-key text-gray-400"></i>
                    </div>
                    <input type="text" id="code" name="code" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-center text-2xl font-bold tracking-[0.5em]" required autofocus>
                </div>
                <p class="text-xs text-gray-500 mt-1">Revisa tu bandeja de entrada o spam</p>
            </div>

            <div>
                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 transition duration-300 shadow-md flex items-center justify-center">
                    <span>Verificar Codigo</span>
                    <i class="fa-solid fa-check ml-2"></i>
                </button>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-600 mb-2">No recibiste el codigo?</p>
            <form method="POST" action="{{ route('password.email') }}" class="inline">
                @csrf
                <input type="hidden" name="email" value="{{ $email ?? request('email') }}">
                <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800 transition-colors font-medium">
                    <i class="fa-solid fa-rotate mr-1"></i>Reenviar codigo
                </button>
            </form>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-xs text-gray-500 hover:text-gray-700 transition-colors">
                <i class="fa-solid fa-arrow-left mr-1"></i>Volver a Iniciar Sesion
            </a>
        </div>
    </div>
</body>
</html>
