<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory, SoftDeletes;
    const storageFolder= 'Course';
    protected $fillable = [
        'grade_id',
        'month_id',
        'nameOfCourse',
        'img',
        'price',
        'description',
        'numOfLessons',
        'numOfExams',
        'creationDate',
        'status'

    ];
    protected $dates = ['creationDate'];

    public function mainCourse()
    {
        return $this->belongsTo(MainCourse::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
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
                    ->withPivot('purchase_date', 'status','byAdmin');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }
}
