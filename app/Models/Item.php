<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // <-- 1. Importación necesaria para generar texto aleatorio

class Item extends Model
{
    use HasFactory;

    // <-- 2. Agregamos 'barcode' al arreglo para que Laravel permita guardarlo
    protected $fillable = ['name', 'total_count', 'photo_url', 'is_active', 'barcode']; 

    // <-- 3. El "Cerebro" automático: se ejecuta justo antes de guardar en la base de datos
    protected static function booted()
    {
        static::creating(function ($item) {
            // Genera el código automáticamente con el prefijo de la universidad
            $item->barcode = 'UAS-INV-' . strtoupper(Str::random(6));
        });
    }

    public function loans() {
        return $this->hasMany(Loan::class);
    }
    
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