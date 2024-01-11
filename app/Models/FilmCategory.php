<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FilmCategory extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'film_id',
        'category_id'
    ];

    public function films()
    {
        return $this->belongsTo(Film::class,'film_id','id');
    }

    public function categories()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }
}
