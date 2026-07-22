<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'badge', 'min_spent', 'discount_percent'];
}
