<?php

namespace App\Models;


use Illuminate\Support\Str; 

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Parnt  extends Authenticatable  implements JWTSubject
{
    use HasFactory , Notifiable, SoftDeletes ;

    protected $table = 'parnts';
    const storageFolder= 'Parent';

    protected $fillable = [
        'name',
        'email',
        'password',
        'parentPhoNum',
        'code',
        'img',
        'email_verified_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($parnt) {
            $parnt->code = self::generateRandomCode();
        });
    }

    private static function generateRandomCode()
    {
        return Str::random(10);
    }


    public function users()
    {
        return $this->hasMany(User::class, 'parnt_id');
    }

    public function reactions()
{
    return $this->morphMany(Reaction::class, 'reactable');
}


//     public function hasStudent($studentId)
// {
//     return $this->users()->where('id', $studentId)->exists();
// }

    public function students()
{
    return $this->hasMany(Student::class, 'parent_id', 'id'); // assuming parent_id is foreign key
}

    public function contactUs()
    {
        return $this->hasMany(ContactUs::class);
    }


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $cast = [
        'password'=>'hashed'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
