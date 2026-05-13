<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ComputerRoom extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'capacity', 'location', 'is_active', 'photo_url'];
    public function reservations()
    {
        return $this->hasMany(RoomReservation::class);
    }
    public function isAvailable($start, $end)
    {
        $occupiedCount = $this->reservations()
            ->whereIn('status', ['active', 'pending'])
            ->where(function ($query) use ($start, $end) {
                $query->where('start_date', '<', $end)
                      ->where('end_date', '>', $start);
            })
            ->count();
        return $occupiedCount < $this->capacity;
    }
}