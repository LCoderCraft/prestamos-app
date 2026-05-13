<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class SupportChat extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'subject', 'status', 'last_message_at'];
    protected $casts = [
        'last_message_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
    public function unreadAdminMessages()
    {
        return $this->messages()->where('is_admin', false)->whereNull('read_at');
    }
    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }
}