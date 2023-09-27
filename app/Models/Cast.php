<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cast extends Model
{
    use HasFactory;
    protected $fillable = [
        'film_id',
        'artist_id',
        'character',
        'position',
        'image',
        'status'

    ];

    public function films()
    {
        return $this->belongsTo(Film::class,'film_id','id');
    }
    public function artists()
    {
        return $this->belongsTo(Artist::class,'artist_id','id');
    }
}
