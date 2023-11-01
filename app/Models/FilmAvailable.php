<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilmAvailable extends Model
{
    use HasFactory;
    protected $fillable = [
        'film_id',
        'available_id',
        'url'
    ];

    public function film()
    {
        return $this->belongsTo(Film::class,'film_id','id');
    }

    public function availables()
    {
        return $this->belongsTo(AvailableIn::class,'available_id','id');
    }
}
