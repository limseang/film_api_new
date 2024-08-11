<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use DateTime;
use App\Traits\AlibabaStorage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;
class Film extends Model
{
    use HasFactory , SoftDeletes, AlibabaStorage, LogsActivity;
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
        'genre_name',
        'distributor_name',
        'director_name',
        'tag_name',
        'film_category_name',
        'poster_image',
        'cover_image',
        'multiple_category',
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category','id');
    }
    public function tags()
    {
        return $this->belongsTo(Tag::class,'tag','id');
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

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id','id');
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

public function subtitles()
{
    return $this->hasMany(EpisodeSubtitle::class, 'film_id', 'id');
}

    public function getReleaseDateFormatAttribute()
    {
        return date('d/m/Y', strtotime($this->release_date));
    }

 
    public function getGenreNameAttribute()
    {
        return $this->genre->name ?? '';
    }

    public function getDistributorNameAttribute()
    {
        return $this->distributors->name ?? '';
    }

    public function getDirectorNameAttribute()
    {
        return $this->directors->name ?? '';
    }

    public function getTagNameAttribute()
    {
        return $this->tags->name ?? '';
    }

    public function getFilmCategoryNameAttribute()
    {
        return $this->categories->name ?? '';
    }

    public function getPosterImageAttribute()
    {
        return $this->poster ? $this->getSignedUrl($this->poster) : '';
    }

    public function getCoverImageAttribute()
    {
        return $this->cover ? $this->getSignedUrl($this->cover) : '';
    }

    public function getMultipleCategoryAttribute()
    {
        return $this->filmCategories->pluck('name')->toArray();
    }
    

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    protected static $dontSubmitEmptyLogs = true;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName($this->table)
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    public function tapActivity(Activity $activity)
    {
        $activity->default_field    = "{$this->name}";
        $activity->log_name         = $this->table;
        $activity->causer_id        = Auth::user()->id ?? null;
    }



}
