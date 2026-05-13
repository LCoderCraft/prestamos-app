<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ChatMessage extends Model
{
    use HasFactory;
    protected $fillable = ['support_chat_id', 'user_id', 'body', 'is_admin', 'read_at'];
    protected $casts = [
        'read_at' => 'datetime',
        'is_admin' => 'boolean',
    ];
    public function supportChat()
    {
        return $this->belongsTo(SupportChat::class);
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}