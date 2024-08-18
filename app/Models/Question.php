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
        'exam_id'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }



    public function choices()
    {
        return $this->hasMany(Choice::class);
    }


}
