<?php

namespace App\Models;

use App\Http\Controllers\UploadController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artical extends Model
{
    use HasFactory, SoftDeletes;
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
        'film_id',
        'tag_id',

    ];

    /**
     * @return BelongsTo
     */
    public function origin(): BelongsTo
    {
        return $this->belongsTo(Origin::class);

    }
    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class,'item_id','id')->where('type',1);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }
    public function tag(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function categoryArtical()
    {
        return $this->hasMany(CategoryArtical::class);
    }

    public function BookMark(): HasMany
    {
        return $this->hasMany(BookMark::class,'post_id','id');
    }



}
