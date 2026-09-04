<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_weight',
        'max_weight',
        'markup_price',
    ];

    protected $casts = [
        'min_weight' => 'integer',
        'max_weight' => 'integer',
        'markup_price' => 'integer',
    ];
}
