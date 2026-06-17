<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['name','phone','role','email','pin','access','outlet_id','photo'];

    protected $hidden = ['pin'];

    public function setPinAttribute($value)
    {
        $this->attributes['pin'] = $value ? bcrypt($value) : null;
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }
}
