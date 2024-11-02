<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory, SoftDeletes;
    const storageFolder= 'Messages';
    protected $fillable = [
        'chat_id',
        'message',
        'sender_id',
        'sender_type',
        'creationDate',
        'img',
        'video',
        'url',
        'pdf'
    ];

    protected $dates = ['creationDate'];

    public function reactions()
{
    return $this->hasMany(Reaction::class);
}


    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->morphTo();
    }

}
