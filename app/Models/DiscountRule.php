<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountRule extends Model
{
    protected $fillable = ['min_purchase', 'discount_percent', 'outlet_id'];
}
