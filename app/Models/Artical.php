<?php

namespace App\Models;

use App\Http\Controllers\UploadController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\AlibabaStorage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

class Artical extends Model
{
    use HasFactory, SoftDeletes, AlibabaStorage, LogsActivity;

    protected $table = 'articals';
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

    protected $appends = [
        'image_url',
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
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class, 'film_id', 'id');
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'item_id', 'id')->where('type', 1);
    }

    /**
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tag(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'artical_tag', 'artical_id', 'tag_id');
    }

    /**
     * @return HasMany
     */
    public function categoryArtical(): HasMany
    {
        return $this->hasMany(CategoryArtical::class);
    }

    /**
     * @return HasMany
     */
    public function BookMark(): HasMany
    {
        return $this->hasMany(BookMark::class, 'post_id', 'id');
    }

    /**
     * Get the image URL attribute.
     *
     * @return string|null
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? $this->getSignedUrl($this->image) : null;
    }

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;
    protected static $dontSubmitEmptyLogs = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName($this->table)
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity)
    {
        $activity->default_field = "{$this->title}";
        $activity->log_name = $this->table;
        $activity->causer_id = Auth::user()->id ?? null; // Add null check for Auth
    }
}
