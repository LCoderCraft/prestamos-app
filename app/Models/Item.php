<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

    // pongo barcode aqui para que laravel me deje guardarlo en la base de datos
    protected $fillable = ['name', 'total_count', 'photo_url', 'is_active', 'barcode']; 

    // esto se ejecuta solo cuando voy a crear un equipo nuevo
    // asi me aseguro que siempre tenga un codigo de barras desde el principio
    protected static function booted()
    {
        static::creating(function ($item) {
            $item->barcode = 'UAS-INV-' . strtoupper(Str::random(6));
        });
    }

    public function loans() {
        return $this->hasMany(Loan::class);
    }
    
    // funcion para saber si hay equipos disponibles en un horario
    // cuenta cuantos prestamos estan activos o pendientes en ese rango
    // si el numero es menor al total de equipos, entonces si hay disponible
    public function isAvailable($start, $end) {
        $occupiedCount = $this->loans()
            ->whereIn('status', ['active', 'pending'])
            ->where(function($query) use ($start, $end) {
                $query->where('start_date', '<', $end)
                      ->where('end_date', '>', $start);
            })
            ->count();
        return $occupiedCount < $this->total_count;
    }
}