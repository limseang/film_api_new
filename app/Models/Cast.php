<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AlibabaStorage;

class Cast extends Model
{
    use HasFactory , SoftDeletes, AlibabaStorage;
    protected $table='casts';
    protected $fillable = [
        'film_id',
        'actor_id',
        'character',
        'position',
        'image',
        'status'
    ];

    protected $appends = [
        'image_url',
        'film_name',
        'actor_name'
    ];

    public function films()
    {
        return $this->belongsToMany(Film::class,'film_id','id');
    }

    public function film()
    {
        return $this->belongsTo(Film::class,'film_id','id');
    }
    public function artists()
    {
        return $this->belongsTo(Artist::class,'actor_id','id');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? $this->getSignedUrl($this->image) : null;
    }

    public function getFilmNameAttribute()
    {
        return $this->film ? $this->film->title : null;
    }

    public function getActorNameAttribute()
    {
        return $this->artists ? $this->artists->name : null;
    }

}
