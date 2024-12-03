<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymobTransaction extends Model
{
    protected $fillable = [
        'special_reference',
        'paymob_order_id',
        'payment_method_id',
        'user_id',
        'course_id',
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
    
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
