<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'admin_id',
        'parnt_id',
        'student_id'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function parent()
    {
        return $this->belongsTo(Parnt::class, 'parnt_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
