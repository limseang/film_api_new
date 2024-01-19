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

    protected $appends =[
        'nationality_name'
    ];

   public function country()
   {
       return $this->belongsTo(Country::class,'nationality','id');
   }

 public function casts()
 {
     // deleted_at is null
        return $this->belongsToMany(Film::class,'casts','actor_id','film_id')->whereNull('casts.deleted_at');
 }

 public function films()
 {
        return $this->belongsToMany(Film::class,'casts','actor_id','film_id');
 }
 public function getNationalityNameAttribute()
 {
     return $this->country->name ?? '';

 }






}
