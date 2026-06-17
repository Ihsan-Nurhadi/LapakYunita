<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = ['transaction_id','product_id','name','qty','price'];

    public function transaction()
    {
        return $this->belongsTo(PosTransaction::class, 'transaction_id');
    }
}
