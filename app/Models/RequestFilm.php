<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestFilm extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'film_name',
        'film_link',
        'film_image',
        'film_description',
        'noted',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
