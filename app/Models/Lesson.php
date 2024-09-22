<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;
    const storageFolder= 'Lessons';
    protected $fillable = [
        'grade_id',
        'title',
        'poster',
        'video',
        'ExplainPdf',
        'lec_id',
        'duration',
        'numOfPdf',
        // 'month_id',
        'course_id',
        'description'
    ];

    public function lec()
    {
        return $this->belongsTo(Lec::class, 'lec_id');
    }


    public function students()
    {
        return $this->belongsToMany(Student::class,'student_lessons');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class,'exam_lessons');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function exam()
    {
        return $this->hasOne(Exam::class, 'lesson_id');
    }
    
}
