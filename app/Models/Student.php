<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'isPay',
        'user_id',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }




    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'student_lessons');
    }




   
}
