<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContinueToWatch extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'film_id',
        'film_type',
        'episode_id',
        'duration',
        'progressing',
        'watched_at',
        'episode_number',
    ];

    public function films()
    {
        return $this->belongsTo(Film::class, 'film_id', 'id');
    }

    public function episodes()
    {
        return $this->belongsTo(Episode::class, 'episode_id', 'id');
    }

//    public function user()
//    {
//        return $this->belongsTo(User::class, 'user_id', 'id');
//    }

    public function getProgressingAttribute($value)
    {
        return $value ? json_decode($value) : null;
    }

    public function subtitles()
    {
        return $this->hasOne(EpisodeSubtitle::class,'episode_id','episode_id');
    }
}
