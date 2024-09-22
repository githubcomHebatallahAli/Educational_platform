<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Month extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
    ];

    public function mainCourses()
    {
        return $this->hasMany(MainCourse::class);
    }
    
    public function exams()
    {
        return $this->hasMany(User::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

}
