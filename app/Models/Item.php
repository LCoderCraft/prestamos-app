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
    
    // Lógica mágica para ver disponibilidad
    public function isAvailable($start, $end) {
        $activeLoans = $this->loans()
            ->whereIn('status', ['active', 'pending'])
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end])
                      ->orWhere(function($q) use ($start, $end){
                          $q->where('start_date', '<', $start)
                            ->where('end_date', '>', $end);
                      });
            })
            ->count();
            
        return $activeLoans < $this->total_count;
    }
}