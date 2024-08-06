<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AlibabaStorage;

class Distributor extends Model
{
    use HasFactory, AlibabaStorage;
    protected $fillable = [
        'name',
        'description',
        'image',
        'status'
    ];

    protected $appends = [
        'image_url',
        'total_film'
    ];

    public function films()
    {
        return $this->hasMany(Film::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? $this->getSignedUrl($this->image) : null;
    }

    public function getTotalFilmAttribute()
    {
        return $this->films->count() ?? 0;
    }
}
