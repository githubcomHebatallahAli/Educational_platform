<?php

namespace App\Models;


use Illuminate\Notifications\Notifiable;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Parnt  extends Authenticatable  implements JWTSubject
{
    use HasFactory , Notifiable, SoftDeletes ;

    protected $fillable = [
        'name',
        'email',
        'password',
        'parentPhoNum',
        'email_verified_at'
    ];
    public function users()
    {
        return $this->hasMany(User::class);
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
