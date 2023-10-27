<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cast extends Model
{
    use HasFactory;
    protected $table='casts';
    protected $fillable = [
        'film_id',
        'actor_id',
        'character',
        'position',
        'image',
        'status'

    ];

    public function films()
    {
        return $this->belongsToMany(Film::class,'film_id','id');
    }
    public function artists()
    {
        return $this->belongsTo(Artist::class,'actor_id','id');
    }
}
