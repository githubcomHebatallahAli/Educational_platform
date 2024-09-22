<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentExam extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'exam_id',
        'score',
        'has_attempted'
    ];

    public function isAbsent()
    {
        return $this->score === null;
    }
}
