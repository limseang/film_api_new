<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class video extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'video_url',
        'view_count',
        'like_count',
        'cover_image_url',
        'status',
        'film_id',
        'article_id',
        'type_id',
        'category_id',
        'tag_id',
        'running_time',
        'status'
    ];

    public function film()
    {
        return $this->belongsTo(Film::class, 'film_id'  , 'id');
    }

    public function article()
    {
        return $this->belongsTo(Artical::class, 'article_id'  , 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class,);
    }

    public function bookmarks()
    {
        return $this->hasMany(BookMark::class,);
    }

    public function likes()
    {
        return $this->hasMany(Like::class,);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'video_tag', 'video_id', 'tag_id');
    }

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id'  , 'id' );
    }

    public function types()
    {
        return $this->belongsTo(Type::class,  'type_id', 'id');
    }

}
