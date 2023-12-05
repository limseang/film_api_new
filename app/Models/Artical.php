<?php

namespace App\Models;

use App\Http\Controllers\UploadController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artical extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'origin_id',
        'category_id',
        'image',
        'type_id',
        'like',
        'comment',
        'share',
        'profile',
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
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    public function tag()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function categoryArtical()
    {
        return $this->hasMany(CategoryArtical::class);
    }

    public function BookMark()
    {
        return $this->hasMany(BookMark::class,'post_id','id');
    }



}
