<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Préstamos FIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-indigo-900 via-gray-800 to-black min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md border-t-4 border-indigo-600 relative overflow-hidden">
        
        <div class="text-center mb-8">
            <div class="bg-indigo-100 text-indigo-700 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i class="fa-solid fa-building-columns text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Préstamos FIM</h2>
            <p class="text-sm text-gray-500 mt-1">Facultad de Ingeniería Mochis</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 text-sm flex items-start shadow-sm">
                <i class="fa-solid fa-circle-exclamation mt-0.5 mr-2"></i>
                <ul class="list-none p-0 m-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-5">
                <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Número de Identificador / Usuario</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-id-card text-gray-400"></i>
                    </div>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Ej: 12345678" class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" required autofocus>
                </div>
            </div>
            
            <div class="mb-5">
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block text-gray-700 text-sm font-semibold">Contraseña</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-indigo-600 hover:text-indigo-800 transition-colors font-medium">¿Olvidaste tu contraseña?</a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" id="password" name="password" placeholder="••••••••" class="w-full pl-10 pr-10 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" required>
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-600 focus:outline-none transition-colors">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center mb-6">
                <input id="remember_me" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer" name="remember">
                <label for="remember_me" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                    Mantener sesión iniciada
                </label>
            </div>

            <div>
                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 transition duration-300 shadow-md flex justify-center items-center">
                    <span>Acceder al Sistema</span>
                    <i class="fa-solid fa-arrow-right-to-bracket ml-2"></i>
                </button>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-600 mb-3">¿Eres de nuevo ingreso o no tienes cuenta?</p>
            <a href="{{ route('register') }}" class="inline-block w-full py-2 px-4 border-2 border-indigo-100 text-indigo-600 font-semibold rounded-lg hover:bg-indigo-50 hover:border-indigo-200 transition duration-200">
                Solicitar Registro
            </a>
        </div>
    </div>

    <div class="fixed bottom-4 right-4 z-50">
        <button onclick="openSupport()" class="bg-gray-800 text-white p-3 rounded-full shadow-lg hover:bg-gray-700 hover:shadow-xl transition-all duration-300 flex items-center group">
            <i class="fa-solid fa-headset text-xl"></i>
            <span class="max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-300 ease-in-out whitespace-nowrap group-hover:ml-2 font-medium">
                Soporte Técnico
            </span>
        </button>
    </div>

    <script>
        // Función para alternar la visibilidad de la contraseña
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Función placeholder para el botón de soporte
        function openSupport() {
            alert('Módulo de soporte técnico en desarrollo. \n\nPara ayuda inmediata sobre préstamos de equipo, contacta a la administración de la FIM.');
        }
    </script>
</body>
</html>