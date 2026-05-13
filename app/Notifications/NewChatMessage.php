<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\SupportChat;
use App\Models\ChatMessage;
class NewChatMessage extends Notification
{
    use Queueable;
    public $chat;
    public $message;
    public function __construct(SupportChat $chat, ChatMessage $message)
    {
        $this->chat = $chat;
        $this->message = $message;
    }
    public function via(object $notifiable): array
    {
        return ['database'];
    }
    public function toArray(object $notifiable): array
    {
        $isAdmin = $notifiable->role === 'admin';
        return [
            'message' => $isAdmin
                ? 'Nuevo mensaje de ' . $this->chat->user->username
                : 'Respuesta de Soporte FIM',
            'item' => 'Chat #' . $this->chat->id,
            'chat_id' => $this->chat->id,
            'body' => $this->message->body,
            'action_url' => $isAdmin
                ? route('admin.support.chat', $this->chat->id)
                : route('support.chat.show', $this->chat->id),
        ];
    }
}