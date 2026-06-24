<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name','price','modal','category','image'];

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'product_outlet')
                    ->withPivot('stock')
                    ->withTimestamps();
    }
}
