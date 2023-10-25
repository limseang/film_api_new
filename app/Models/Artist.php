<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'birth_date',
        'death_date',
        'nationality',
        'profile',
        'biography',
        'known_for',
        'status',
        'film'
    ];

   public function country()
   {
       return $this->belongsTo(Country::class,'nationality','id');
   }

    public function films()
    {
         return $this->hasMany(Film::class);
    }



}
