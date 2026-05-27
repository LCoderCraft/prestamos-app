<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// modelo para los centros de computo
// cada centro tiene nombre, capacidad (cuantas computadoras), ubicacion y si esta activo
class ComputerRoom extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'capacity', 'location', 'is_active', 'photo_url'];

    // un centro tiene muchas reservaciones
    public function reservations()
    {
        return $this->hasMany(RoomReservation::class);
    }

    // misma logica que en Item, pero aqui checa la capacidad del centro
    // si los lugares ocupados son menos que la capacidad, hay espacio
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