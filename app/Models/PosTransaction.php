<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosTransaction extends Model
{
    use HasFactory;

    protected $table = 'pos_transactions';
    protected $fillable = ['trx_id','total','paid','change','cashier','outlet'];

    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }
}
