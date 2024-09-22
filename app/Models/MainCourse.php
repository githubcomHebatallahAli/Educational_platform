<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MainCourse extends Model
{
    use HasFactory, SoftDeletes;
    const storageFolder= 'MainCourse';
    protected $fillable = [
        'grade_id',
        'month_id',
        'nameOfCourse',
        'img',
        'price',
        // 'description'
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }



}
