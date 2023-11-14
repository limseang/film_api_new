<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'film_id',
        'title',
        'description',
        'episode',
        'season',
        'file',
    ];

    public function film()
    {
        return $this->belongsTo(Film::class,'film_id','id');
    }
}
