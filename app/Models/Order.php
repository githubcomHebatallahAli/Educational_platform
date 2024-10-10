<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'course_id',
        'price',
        'purchase_date',
        'status',
        'payment_method'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
