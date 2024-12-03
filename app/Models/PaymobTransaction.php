<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymobTransaction extends Model
{
    protected $fillable = [
        'merchant_order_reference',
        'special_reference',
        'paymob_order_id',
        'payment_method_id',
        'user_id',
        'price',
        'currency',
        'status'
    ];


    public function paymentMethod()
    {
        return $this->belongsTo(PaymobMethod::class, 'payment_method_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
