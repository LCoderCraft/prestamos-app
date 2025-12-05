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