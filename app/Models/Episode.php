<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AlibabaStorage;

class Episode extends Model
{
    use HasFactory , SoftDeletes, AlibabaStorage;
    protected $fillable = [
        'id',
        'film_id',
        'title',
        'description',
        'episode',
        'season',
        'file',
    ];

    protected $appends = [
        'is_subtitled',
        'poster_image',
    ];

    public function film()
    {
        return $this->belongsTo(Film::class,'film_id','id');
    }

    public function subtitles()
    {
        return $this->hasMany(EpisodeSubtitle::class);
    }

    public function getIsSubtitledAttribute()
    {
        return $this->subtitles()->exists();
    }

    public function getPosterImageAttribute()
    {
        return $this->poster ? $this->getSignedUrl($this->poster) : '';
    }
}
