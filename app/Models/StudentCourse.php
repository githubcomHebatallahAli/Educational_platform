<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentCourse extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'course_id',
        'purchase_date',
        'status',
        'byAdmin'
    ];

    protected $date = ['purchase_date'];

    public function getPurchaseDateAttribute($value)
{
    return Carbon::parse($value)
            ->timezone('Africa/Cairo')
            ->format('Y-m-d H:i:s');
}

}
