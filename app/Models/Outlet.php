<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = ['name','phone','address','kelurahan','kode_pos','image'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_outlet')
                    ->withPivot('stock')
                    ->withTimestamps();
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
