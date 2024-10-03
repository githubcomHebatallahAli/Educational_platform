<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable , SoftDeletes;
    const storageFolder= 'Student';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'studentPhoNum',
        'parentPhoNum',
        'grade_id',
        'governorate',
        'parnt_id',
        'img',
        'parent_code',
        'email_verified_at',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    public function exams()
    {
        return $this->belongsToMany(Exam::class,'student_exams')
                    ->withPivot('score', 'has_attempted','started_at','submitted_at','time_taken')
                    ->withTimestamps();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'student_courses')
                    ->withPivot('purchase_date', 'status');
    }

    // protected $cast = [
    //     'password'=>'hashed'
    // ];
      /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    public function student()
    {
        return $this->hasMany(Student::class);
    }



    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }


    public function parent()
{
    return $this->belongsTo(Parnt::class, 'parnt_id');
}




    public function Answers()
    {
        return $this->hasMany(Answer::class, 'user_id');
    }


}

