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
        'grade',
        'title',
        'poster',
        'video',
        'ExplainPdf',
    ];


    public function students()
    {
        return $this->belongsToMany(Student::class,'student_lessons');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
