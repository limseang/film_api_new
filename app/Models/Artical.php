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

    protected $table ='articals';
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

    protected $append =[
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
        return $this->belongsToMany(Tag::class,'artical_tag','artical_id','tag_id');
    }

    public function categoryArtical()
    {
        return $this->hasMany(CategoryArtical::class);
    }

    public function BookMark(): HasMany
    {
        return $this->hasMany(BookMark::class,'post_id','id');
    }

    public function getImageUrlAttribute()
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
        $activity->default_field    = "{$this->title}";
        $activity->log_name         = $this->table;
        $activity->causer_id        = Auth::user()->id;
    }


}
