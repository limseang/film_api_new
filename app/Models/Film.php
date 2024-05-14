<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use DateTime;
class Film extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'id',
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
        'cover',
        'trailer',
        'release_date'
    ];
    protected $appends=[
        'release_date_format',
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
        return $this->belongsToMany(Category::class,'film_categories','film_id','category_id');
    }

    public function rate(){
        return $this->hasMany(Rate::class);
    }

    public function cast(){
        return $this->hasMany(Cast::class,'film_id','id');
    }

    public function filmAvailable(){
        return $this->hasMany(FilmAvailable::class,'film_id','id');
    }

    public function episode(){
        return $this->hasMany(Episode::class,'film_id','id');
    }

    public function filmComment(){
        return $this->hasMany(Comment::class,'item_id','id')->where('type',2);
    }

    public function getReleaseDateFormatAttribute()
    {
        $date = DateTime::createFromFormat('d/m/Y', $this->attributes['release_date']);
        return $date->format('d/m/Y');
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class,'genre_id','id');
    }
    public function distributors()
    {
        return $this->belongsTo(Distributor::class,'distributor_id','id');
    }

    public function continueToWatch()
    {
        return $this->hasMany(ContinueToWatch::class,'film_id','id');
    }

//    public function favorite()
//    {
//        return $this->hasMany(Farvorite::class,'item_id','id')->where('item_type',2);
//    }



}
