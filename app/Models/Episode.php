<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Episode extends Model
{
    use HasFactory , SoftDeletes;
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
}
