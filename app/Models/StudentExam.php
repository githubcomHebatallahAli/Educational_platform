<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentExam extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'exam_id',
        'score',
        'has_attempted',
        'correctAnswers',
        'started_at',
        'submitted_at',
        'time_taken',
    ];


    protected $dates = ['started_at', 'submitted_at'];

    public function getStartedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }


    public function getSubmittedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }


}
