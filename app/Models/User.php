<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
//sof delete
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;


    protected $fillable = [
        'name',
        'email',
        'role_id',
        'phone',
        // 'telegram',
        // 'avatar',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password',
        // 'role_id',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',

    ];

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    //1 user has 1 role

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function like(){
        return $this->hasMany(Like::class);
    }

    public function Usertype (){
        return $this->belongsTo(UserType::class);
    }
}
