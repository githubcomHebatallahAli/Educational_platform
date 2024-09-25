<?php

namespace App\Models;


use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin  extends Authenticatable  implements JWTSubject
{
    use HasFactory , Notifiable, SoftDeletes ;
    const storageFolder= 'Admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'adminPhoNum',
        'status',
        'subject',
        'img',
        'role_id',
        'email_verified_at'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
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

      public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
