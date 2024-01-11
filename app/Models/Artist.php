<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artist extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'id',
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

 public function casts()
 {
        return $this->hasMany(Cast::class,'actor_id','id');
 }

 public function films()
 {
        return $this->belongsToMany(Film::class,'casts','actor_id','film_id');
 }




}
