<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class RoomReservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'computer_room_id', 'user_id', 'requester_type',
        'group_name', 'teacher_name', 'purpose',
        'start_date', 'end_date', 'status', 'admin_comment'
    ];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
    public function computerRoom()
    {
        return $this->belongsTo(ComputerRoom::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}