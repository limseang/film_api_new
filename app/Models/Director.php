<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Director extends Model
{
    use HasFactory;
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

    public function country(){

        return $this->belongsTo(Country::class);

    }

    public function films(){

        return $this->hasMany(Film::class);

    }

}
