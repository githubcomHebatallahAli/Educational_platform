<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'question',
        'exam_id',
        'choice_1',
        'choice_2',
        'choice_3',
        'choice_4',
        'correct_choice'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }



public function answers()
{
    return $this->hasMany(Answer::class, 'question_id');
}


}
