<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'title',
        'totalMarke',
        'duration',
        'examNumber',
        'grade'

    ];

    public function students()
    {
        return $this->belongsToMany(Student::class)
        ->withPivot('score' , 'has_attempted')
        ->withTimestamps();
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
