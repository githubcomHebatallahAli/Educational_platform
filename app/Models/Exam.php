<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'title',
        'creationDate',
        'duration',
        'grade_id',
        'course_id',
        'test_id',
        'numOfQ',
        'deadLineExam',
        'lesson_id'
    ];

    protected $date = ['creationDate'];

    protected $dates = ['deadLineExam'];

    public function getFormattedDeadLineExamAttribute()
    {
        return Carbon::parse($this->deadLineExam)->format('Y-m-d h:i:s  A');
    }

    public function students()
    {
        return $this->belongsToMany(User::class,'student_exams')
        ->withPivot('score','has_attempted','started_at',
        'submitted_at','time_taken','correctAnswers')
        ->withTimestamps();
    }



    public function questions()
{
    return $this->hasMany(Question::class, 'exam_id');
}

    public function answers()
{
    return $this->hasMany(Answer::class);
}

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    // public function lessons()
    // {
    //     return $this->belongsToMany(Lesson::class,'exam_lessons');
    // }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

}
