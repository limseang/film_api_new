<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpisodeSubtitle extends Model
{
    use HasFactory;

    protected $fillable = [
        'language_id',
        'url',
        'film_id',
        'episode_id'
    ];

    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }

    public function language()
    {
        return $this->belongsTo(Country::class, 'language_id', 'id');
    }

    public function continueToWatch()
    {
        return $this->belongsTo(ContinueToWatch::class,'episode_id','episode_id');

    }
}
