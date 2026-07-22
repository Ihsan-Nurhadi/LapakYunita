<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'phone', 'address', 'email', 'dob'];
    protected $appends = ['tier', 'badge', 'tier_discount_percent'];

    protected static $cachedTiers = null;

    protected static function getCachedTiers()
    {
        if (self::$cachedTiers === null) {
            self::$cachedTiers = CustomerTier::orderBy('min_spent', 'desc')->get();
        }
        return self::$cachedTiers;
    }

    public function transactions()
    {
        return $this->hasMany(PosTransaction::class, 'customer_id');
    }

    protected $totalSpent = null;

    protected function getTotalSpent()
    {
        if ($this->totalSpent === null) {
            if (array_key_exists('transactions_sum_total', $this->attributes)) {
                $this->totalSpent = $this->transactions_sum_total ?? 0;
            } else {
                $this->totalSpent = $this->transactions()->where('is_draft', false)->sum('total') ?? 0;
            }
        }
        return $this->totalSpent;
    }

    public function getTierAttribute()
    {
        $spent = $this->getTotalSpent();
        $tiers = self::getCachedTiers();
        foreach ($tiers as $tier) {
            if ($spent >= $tier->min_spent) {
                return $tier->name;
            }
        }
        return 'Silver';
    }

    public function getBadgeAttribute()
    {
        $spent = $this->getTotalSpent();
        $tiers = self::getCachedTiers();
        foreach ($tiers as $tier) {
            if ($spent >= $tier->min_spent) {
                return $tier->badge;
            }
        }
        return '🥈';
    }

    public function getTierDiscountPercentAttribute()
    {
        $spent = $this->getTotalSpent();
        $tiers = self::getCachedTiers();
        foreach ($tiers as $tier) {
            if ($spent >= $tier->min_spent) {
                return $tier->discount_percent;
            }
        }
        return 0;
    }
}
