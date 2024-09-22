<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lec extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'lec_id');  // تأكد من استخدام العمود الصحيح
    }

}
