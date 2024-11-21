<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymobMethod extends Model
{
    protected $fillable = [
        'payment_method',
        'api_key',
        'integration_id',
        'currency',
        'status'
        ];

    public function transactions()
    {
        return $this->hasMany(PaymobTransaction::class, 'payment_method_id');
    }
}
