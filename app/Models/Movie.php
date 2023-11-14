<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;
     protected $fillable = [
        'title',
        'description',
        'poster',
        'trailer',
        'running_time',
        'language',
        'release_date',
        'status',
        'type',
        'rating',
        'director',
        'cast',

     ];
    public function availables()
    {
        return $this->hasMany(FilmAvailable::class,'film_id','id');
    }
    public function categories()
    {
        return $this->hasMany(FilmCategory::class,'film_id','id');
    }



}
