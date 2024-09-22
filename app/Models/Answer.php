<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Answer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'exam_id',
        'question_id',
        'selected_choice'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }


    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }


}
