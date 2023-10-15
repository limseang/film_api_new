<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'overview',
        'director',
        'cast',
        'category',
        'tag',
        'rating',
        'type',
        'status',
        'runtime',
        'review',
        'language',
        'movie',
        'poster',
        'trailer',
        'release_date'
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class);
    }
    public function tags()
    {
        return $this->belongsTo(Tag::class,'tag','id');
    }
    public function ratings()
    {
        return $this->belongsTo(Rating::class,'rating','id');
    }
    public function types()
    {
        return $this->belongsTo(Type::class,'type','id');
    }
    public function languages()
    {
        return $this->belongsTo(Country::class,'language','id');
    }

    public function casts()
    {
        return $this->hasMany(Cast::class);
    }

    public function directors()
    {
        return $this->belongsTo(Director::class,'director','id');
    }

    public function filmCategories()
    {
        return $this->hasMany(FilmCategory::class);
    }

    public function rate(){
        return $this->hasMany(Rate::class);
    }

}
