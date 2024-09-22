<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'main_course_id',
        'description',
        'numOfLessons',
        'numOfExams',
        'creationDate',

    ];
    protected $dates = ['creationDate'];

    public function mainCourse()
    {
        return $this->belongsTo(MainCourse::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }


    public function students()
    {
        return $this->belongsToMany(User::class,'student_courses')
                    ->withPivot('purchase_date', 'status');
    }
}
