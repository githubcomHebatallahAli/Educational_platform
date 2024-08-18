<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Answer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'student_id',
        'exam_id',
        'question_id',
        'choice_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }


    public function question()
    {
        return $this->belongsTo(Question::class);
    }


    public function choice()
    {
        return $this->belongsTo(Choice::class);
    }

}
