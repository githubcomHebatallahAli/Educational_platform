<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'grade'
    ];

    const grade = [
       'firstGrade',
       'secondGrade',
       'thirdGrade'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function Courses()
    {
        return $this->hasMany(Course::class);
    }

}
