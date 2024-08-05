<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AlibabaStorage;

class Director extends Model
{
    use HasFactory, AlibabaStorage;
    protected $fillable = [
        'name',
        'birth_date',
        'death_date',
        'biography',
        'known_for',
        'avatar',
        'know_for',
        'nationality',
        'status'
    ];

    protected $appends = [
        'avatar_url',
        'nationality_name'
    ];
    public function country(){

        return $this->belongsTo(Country::class, 'nationality');

    }

    public function films(){

        return $this->hasMany(Film::class);

    }

    public function getAvatarUrlAttribute(){

        return $this->avatar ? $this->getSignedUrl($this->avatar) : null;
    }

    public function getNationalityNameAttribute(){

        return $this->country ? $this->country->name : null;
    }

}
