<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artical extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'origin',
        'category',
        'image',
        'type',
        'like',
        'comment',
        'share',
        'view',
        'film'

    ];

    public function origin()
    {
        return $this->belongsTo(Origin::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function like()
    {
        return $this->hasMany(Like::class);
    }
    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
