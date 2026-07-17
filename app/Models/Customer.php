<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'phone', 'address'];
    protected $appends = ['tier', 'badge'];

    public function transactions()
    {
        return $this->hasMany(PosTransaction::class, 'customer_id');
    }

    public function getTierAttribute()
    {
        $spent = $this->transactions_sum_total ?? 0;
        
        if ($spent > 2000000) {
            return 'Platinum';
        } elseif ($spent >= 500000) {
            return 'Gold';
        } else {
            return 'Silver';
        }
    }

    public function getBadgeAttribute()
    {
        $tier = $this->tier;
        if ($tier === 'Platinum') return '💎';
        if ($tier === 'Gold') return '🥇';
        return '🥈';
    }
}
