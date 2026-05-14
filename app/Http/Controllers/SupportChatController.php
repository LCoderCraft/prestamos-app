<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\SupportChat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Notifications\NewChatMessage;
use Illuminate\Support\Facades\Notification;
class SupportChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->role === 'admin') {
            $chats = SupportChat::with(['user', 'lastMessage'])
                ->orderBy('last_message_at', 'desc')
                ->get();
            $activeChat = null;
            $messages = collect();
        } else {
            $chats = SupportChat::with('lastMessage')
                ->where('user_id', $user->id)
                ->orderBy('last_message_at', 'desc')
                ->get();
            $activeChat = $chats->first();
            $messages = $activeChat ? $activeChat->messages()->with('sender')->oldest()->get() : collect();
        }
        $unreadCount = $user->role === 'admin'
            ? ChatMessage::whereIn('support_chat_id', $chats->pluck('id'))
                ->where('is_admin', false)->whereNull('read_at')->count()
            : 0;
        return view('support.chat', compact('chats', 'activeChat', 'messages', 'unreadCount'));
    }
    public function store(Request $request)
    {
        $request->validate(['subject' => 'required|string|max:255']);
        $chat = SupportChat::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'status' => 'open',
        ]);
        return redirect()->route('support.chat.show', $chat->id);
    }
    public function show($id)
    {
        $chat = SupportChat::findOrFail($id);
        $user = auth()->user();
        if ($user->role !== 'admin' && $chat->user_id !== $user->id) {
            abort(403);
        }
        if ($user->role === 'admin') {
            $chat->messages()->where('is_admin', false)->whereNull('read_at')->update(['read_at' => now()]);
        }
        if ($user->role === 'admin') {
            $chats = SupportChat::with(['user', 'lastMessage'])
                ->orderBy('last_message_at', 'desc')->get();
        } else {
            $chats = SupportChat::with('lastMessage')
                ->where('user_id', $user->id)
                ->orderBy('last_message_at', 'desc')->get();
        }
        $messages = $chat->messages()->with('sender')->oldest()->get();
        $unreadCount = $user->role === 'admin'
            ? ChatMessage::whereIn('support_chat_id', $chats->pluck('id'))
                ->where('is_admin', false)->whereNull('read_at')->count()
            : 0;
        return view('support.chat', compact('chats', 'messages', 'unreadCount'))
            ->with('activeChat', $chat);
    }
    public function sendMessage(Request $request, $id)
    {
        $chat = SupportChat::findOrFail($id);
        $user = auth()->user();
        if ($user->role !== 'admin' && $chat->user_id !== $user->id) {
            abort(403);
        }
        $request->validate(['body' => 'required|string']);
        $message = ChatMessage::create([
            'support_chat_id' => $chat->id,
            'user_id' => $user->id,
            'body' => $request->body,
            'is_admin' => $user->role === 'admin',
        ]);
        $chat->update(['last_message_at' => now()]);
        if ($user->role !== 'admin') {
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewChatMessage($chat, $message));
        } else {
            $chat->user->notify(new \App\Notifications\NewChatMessage($chat, $message));
        }
        if ($request->expectsJson()) {
            return response()->json($message->load('sender'));
        }
        return back();
    }
    public function messages($id)
    {
        $chat = SupportChat::findOrFail($id);
        $after = request('after');
        $query = $chat->messages()->with('sender')->oldest();
        if ($after) {
            $query->where('id', '>', $after);
        }
        return response()->json($query->get());
    }
    public function unreadCount()
    {
        $user = auth()->user();
        if ($user->role !== 'admin') return response()->json(0);
        $count = ChatMessage::whereIn('support_chat_id', SupportChat::where('status', 'open')->pluck('id'))
            ->where('is_admin', false)->whereNull('read_at')->count();
        return response()->json(['count' => $count]);
    }

    public function export($id)
    {
        $chat = SupportChat::with(['user', 'messages.sender'])->findOrFail($id);
        $user = auth()->user();
        if ($user->role !== 'admin' && $chat->user_id !== $user->id) {
            abort(403);
        }
        $content = "=== Conversación: {$chat->subject} ===\n";
        $content .= "Usuario: {$chat->user->username}\n";
        $content .= "Fecha: {$chat->created_at->format('d/m/Y H:i')}\n";
        $content .= "Estado: {$chat->status}\n";
        $content .= str_repeat('=', 50) . "\n\n";
        foreach ($chat->messages as $msg) {
            $sender = $msg->is_admin ? 'Admin' : $chat->user->username;
            $content .= "[{$msg->created_at->format('d/m/Y H:i')}] {$sender}:\n";
            $content .= "{$msg->body}\n\n";
        }
        $content .= str_repeat('=', 50) . "\n";
        $content .= "Fin de la conversación.\n";
        $filename = 'chat-' . $chat->id . '-' . now()->format('Y-m-d') . '.txt';
        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}