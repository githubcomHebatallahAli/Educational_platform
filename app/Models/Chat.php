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
        'user_id',
        'creationDate'
    ];

    protected $dates = ['creationDate'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class , 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(Parnt::class, 'parnt_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class , 'admin_id');
    }
}
