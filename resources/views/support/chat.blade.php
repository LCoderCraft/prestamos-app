<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat de Ayuda - FIM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root { --access-font-size: 100%; }
        body { font-size: var(--access-font-size); }
        .dark body, .dark .bg-gradient-to-br, .dark .bg-gray-50, .dark .bg-gray-100, .dark .bg-gray-200 { background: #1a1a2e !important; }
        .dark .bg-white { background: #16213e !important; }
        .dark .bg-gray-50 { background: #1a1a2e !important; }
        .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-600, .dark .text-gray-500,
        .dark .text-gray-400, .dark .text-indigo-600, .dark .text-indigo-700 { color: #e0e0e0 !important; }
        .dark .border-gray-100, .dark .border-gray-200, .dark .border-gray-300 { border-color: #2a2a4a !important; }
        .dark .divide-gray-100 > * { border-color: #2a2a4a !important; }
        .dark .bg-indigo-50 { background: #1a1a3e !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 min-h-screen font-sans">
    <nav class="bg-indigo-800 shadow-lg border-b-4 border-indigo-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-white text-indigo-800 p-2 rounded-lg shadow-sm">
                    <i class="fa-solid fa-headset text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white tracking-wide">Soporte FIM</h1>
                    <p class="text-xs text-indigo-200 hidden md:block">Centro de ayuda en línea</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="toggleChatDarkMode()" class="text-indigo-300 hover:text-white text-sm px-2 py-1 rounded hover:bg-indigo-700 transition" title="Modo oscuro">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'dashboard') }}"
                   class="text-indigo-200 hover:text-white text-sm flex items-center gap-1 px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </nav>
    
    <div class="max-w-6xl mx-auto p-4 mt-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="height: calc(100vh - 180px);">
            <div class="flex h-full">
                <div class="w-64 border-r border-gray-100 flex flex-col bg-gray-50">
                    <div class="p-3 border-b border-gray-100">
                        <h2 class="text-sm font-bold text-gray-700 flex items-center justify-between">
                            <span><i class="fa-solid fa-comments mr-1"></i> Conversaciones</span>
                            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full" id="total-chats-badge">{{ $chats->count() }}</span>
                        </h2>
                    </div>
                    <div class="flex-1 overflow-y-auto" id="sidebar-chat-list">
                        @forelse($chats as $chat)
                            <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.support.chat' : 'support.chat.show', $chat->id) }}"
                               class="block p-3 border-b border-gray-100 hover:bg-white transition {{ isset($activeChat) && $activeChat->id === $chat->id ? 'bg-white border-l-2 border-l-indigo-500' : '' }}">
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-800">
                                        @if(Auth::user()->role === 'admin')
                                            {{ $chat->user->username }}
                                        @else
                                            Chat #{{ $chat->id }}
                                        @endif
                                    </span>
                                    @if($chat->unreadAdminMessages()->count() > 0)
                                        <span class="bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full chat-unread-badge" data-chat-id="{{ $chat->id }}">
                                            {{ $chat->unreadAdminMessages()->count() }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $chat->subject }}</p>
                                <p class="text-[10px] text-gray-300 mt-0.5">{{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : '' }}</p>
                            </a>
                        @empty
                            <div class="p-6 text-center text-gray-400 text-sm">
                                <i class="fa-solid fa-inbox text-3xl mb-2"></i>
                                <p>Sin conversaciones</p>
                            </div>
                        @endforelse
                    </div>
                    @if(Auth::user()->role !== 'admin')
                    <div class="p-3 border-t border-gray-100">
                        <button onclick="document.getElementById('new-chat-modal').classList.remove('hidden')"
                                class="w-full bg-indigo-600 text-white text-sm py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                            <i class="fa-solid fa-plus"></i> Nuevo chat
                        </button>
                    </div>
                    @endif
                </div>
                
                <div class="flex-1 flex flex-col">
                    @if(isset($activeChat))
                        <div class="p-3 border-b border-gray-100 bg-white flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span class="text-sm font-medium text-gray-800">
                                {{ Auth::user()->role === 'admin' ? $activeChat->user->username : 'Soporte FIM' }}
                            </span>
                            <span class="text-xs text-gray-400 ml-1">En línea</span>
                            <span class="text-xs text-gray-300 ml-auto">{{ $activeChat->subject }}</span>
                            <a href="{{ route('support.chat.export', $activeChat->id) }}" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-600 px-2 py-1 rounded transition flex items-center gap-1" title="Exportar conversación">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="messages-container">
                        @forelse($messages as $msg)
                                <div class="flex {{ $msg->is_admin ? 'justify-end' : 'justify-start' }}" data-msg-id="{{ $msg->id }}">
                                    <div class="max-w-[70%] {{ $msg->is_admin ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-gray-800' }} rounded-xl px-4 py-2 text-sm">
                                        <p>{{ $msg->body }}</p>
                                        <p class="text-[10px] {{ $msg->is_admin ? 'text-indigo-200' : 'text-gray-400' }} mt-1">{{ $msg->created_at->format('H:i') }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-400 text-sm py-10">
                                    <i class="fa-solid fa-comment-dots text-3xl mb-2"></i>
                                    <p>Inicia la conversación</p>
                                </div>
                            @endforelse
                        </div>
                        <form id="chat-form" class="p-3 border-t border-gray-100 bg-white flex gap-2" data-chat-id="{{ $activeChat->id }}">
                            @csrf
                            <input type="text" id="chat-input" autocomplete="off" class="flex-1 border border-gray-200 rounded-full px-4 py-2 text-sm outline-none focus:border-indigo-400 bg-gray-50" placeholder="Escribe tu mensaje..." required>
                            <button type="submit" class="bg-indigo-600 text-white w-10 h-10 rounded-full hover:bg-indigo-700 transition flex items-center justify-center">
                                <i class="fa-solid fa-paper-plane text-sm"></i>
                            </button>
                        </form>
                    @else
                        <div class="flex-1 flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <i class="fa-solid fa-comment-dots text-5xl mb-3 opacity-50"></i>
                                <p class="text-sm">Selecciona una conversación</p>
                                @if(Auth::user()->role !== 'admin')
                                    <button onclick="document.getElementById('new-chat-modal').classList.remove('hidden')" class="mt-3 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">Nuevo chat</button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->role !== 'admin')
    <div id="new-chat-modal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 flex backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="bg-indigo-600 p-4 text-white flex justify-between items-center">
                <h3 class="text-lg font-bold"><i class="fa-solid fa-plus-circle mr-2"></i>Nuevo Chat de Ayuda</h3>
                <button onclick="document.getElementById('new-chat-modal').classList.add('hidden')" class="text-indigo-200 hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <form action="{{ route('support.chat.store') }}" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1 text-gray-700">Asunto</label>
                    <input type="text" name="subject" class="w-full border border-gray-300 p-2.5 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm" placeholder="Ej: Mi préstamo sigue pendiente" required>
                    <p class="text-xs text-gray-400 mt-1">Describe brevemente tu consulta</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('new-chat-modal').classList.add('hidden')" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">Cancelar</button>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Iniciar Chat</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        // Dark mode
        (function() {
            const prefKey = 'chatDarkMode_' + ({{ Auth::user()->role === 'admin' ? 1 : 0 }});
            if (localStorage.getItem(prefKey) === 'true') {
                document.documentElement.classList.add('dark');
            }
        })();
        function toggleChatDarkMode() {
            const prefKey = 'chatDarkMode_' + ({{ Auth::user()->role === 'admin' ? 1 : 0 }});
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem(prefKey, isDark);
        }

        // Enviar mensaje vía AJAX
        const form = document.getElementById('chat-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const input = document.getElementById('chat-input');
                const chatId = this.dataset.chatId;
                if (!input.value.trim()) return;
                fetch('/support/chat/' + chatId + '/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: 'body=' + encodeURIComponent(input.value),
                }).then(r => r.json()).then(data => {
                    input.value = '';
                    const container = document.getElementById('messages-container');
                    const isAdmin = data.is_admin || false;
                    const div = document.createElement('div');
                    div.className = 'flex ' + (isAdmin ? 'justify-end' : 'justify-start');
                    div.setAttribute('data-msg-id', data.id || Date.now());
                    div.innerHTML = '<div class="max-w-[70%] ' + (isAdmin ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-gray-800') + ' rounded-xl px-4 py-2 text-sm"><p>' + (data.body || '') + '</p><p class="text-[10px] ' + (isAdmin ? 'text-indigo-200' : 'text-gray-400') + ' mt-1">Ahora</p></div>';
                    container.appendChild(div);
                    if (data.id) lastMsgId = data.id;
                    container.scrollTop = container.scrollHeight;
                });
            });
        }
        
        // Auto-scroll al fondo inicial
        const container = document.getElementById('messages-container');
        if (container) container.scrollTop = container.scrollHeight;

        // Polling para mensajes de LA CONVERSACIÓN ACTIVA (usando lastId para evitar duplicados)
        @if(isset($activeChat))
        let lastMsgId = {{ $messages->max('id') ?? 0 }};
        setInterval(function() {
            fetch('/support/chat/{{ $activeChat->id }}/messages?after=' + lastMsgId)
                .then(r => r.json())
                .then(messages => {
                    const container = document.getElementById('messages-container');
                    let scrolled = false;
                    messages.forEach(function(msg) {
                        if (msg.id > lastMsgId) lastMsgId = msg.id;
                        const div = document.createElement('div');
                        div.className = 'flex ' + (msg.is_admin ? 'justify-end' : 'justify-start');
                        div.setAttribute('data-msg-id', msg.id);
                        div.innerHTML = '<div class="max-w-[70%] ' + (msg.is_admin ? 'bg-indigo-600 text-white' : 'bg-indigo-50 text-gray-800') + ' rounded-xl px-4 py-2 text-sm"><p>' + msg.body + '</p><p class="text-[10px] ' + (msg.is_admin ? 'text-indigo-200' : 'text-gray-400') + ' mt-1">' + new Date(msg.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'}) + '</p></div>';
                        container.appendChild(div);
                        scrolled = true;
                    });
                    if (scrolled) container.scrollTop = container.scrollHeight;
                });
        }, 3000);
        @endif

        // Notificaciones con sonido y actualización de badges (SOLO ADMIN)
        @if(Auth::user()->role === 'admin')
        let lastKnownUnreadCount = -1; // Estado inicial
        
        setInterval(function() {
            fetch('/support/chat/unread/count')
                .then(r => r.json())
                .then(data => {
                    if (lastKnownUnreadCount === -1) {
                        lastKnownUnreadCount = data.count; // Inicializa sin sonar
                        return;
                    }
                    
                    if (data.count > lastKnownUnreadCount) {
                        // Reproducir sonido si hay un NUEVO mensaje sin leer
                        try {
                            const ctx = new (window.AudioContext || window.webkitAudioContext)();
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.frequency.setValueAtTime(880, ctx.currentTime);
                            osc.frequency.setValueAtTime(660, ctx.currentTime + 0.1);
                            osc.frequency.setValueAtTime(880, ctx.currentTime + 0.2);
                            gain.gain.setValueAtTime(0.3, ctx.currentTime);
                            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
                            osc.start();
                            osc.stop(ctx.currentTime + 0.5);
                        } catch(e) {}
                        
                        // Opcional: Si no hay input pendiente de enviar, refresca los listados del sidebar.
                        const currentInput = document.getElementById('chat-input');
                        if (!currentInput || currentInput.value.trim() === '') {
                             // Pequeño timeout antes de refrescar para que escuchen la notificación.
                             setTimeout(() => window.location.reload(), 1500);
                        }
                    }
                    lastKnownUnreadCount = data.count;
                });
        }, 5000);
        @endif
    </script>
</body>
</html>