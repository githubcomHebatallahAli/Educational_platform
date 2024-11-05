<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'course_id',
        // 'price',
        'purchase_date',
        'status',
        'payment_method'
    ];

    protected $dates = ['purchase_date'];

    public function getFormattedPurchaseDateAttribute()
    {
        return Carbon::parse($this->purchase_date)
        ->timezone('Africa/Cairo')
        ->format('Y-m-d h:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
