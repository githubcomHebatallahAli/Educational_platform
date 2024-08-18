<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactUs extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'phoneNumber',
        'message',
        'user_id',
        'parnt_id'
    ];

       public function user()
    {
        return $this->belongsTo(User::class);
    }
       public function parnt()
    {
        return $this->belongsTo(Parnt::class);
    }
}
