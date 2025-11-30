<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'total_count', 'photo_url', 'is_active'];

    public function loans() {
        return $this->hasMany(Loan::class);
    }
    
    // Verifica si hay stock disponible en un rango de horas
    public function isAvailable($start, $end) {
        $occupiedCount = $this->loans()
            ->whereIn('status', ['active', 'pending'])
            ->where(function($query) use ($start, $end) {
                // Lógica exacta de solapamiento de horarios
                $query->where('start_date', '<', $end)
                      ->where('end_date', '>', $start);
            })
            ->count();
            
        // Si los ocupados son menores al total, hay espacio.
        return $occupiedCount < $this->total_count;
    }
}