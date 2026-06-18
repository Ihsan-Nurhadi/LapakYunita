<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name','price','modal','category','stock','image','outlet_id'];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }
}
