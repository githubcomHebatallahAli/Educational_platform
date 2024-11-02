<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'message_id',
        'reactable_id',
        'reactable_type',
        'type'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function reactable()
    {
        return $this->morphTo();
    }

}
