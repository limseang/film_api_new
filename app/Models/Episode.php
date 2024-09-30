<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AlibabaStorage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

class Episode extends Model
{
    use HasFactory , SoftDeletes, AlibabaStorage, LogsActivity;
    protected $fillable = [
        'id',
        'film_id',
        'title',
        'description',
        'episode',
        'season',
        'file',
        'video_720'
    ];

    protected $appends = [
        'is_subtitled',
        'poster_image',
    ];

    public function film()
    {
        return $this->belongsTo(Film::class,'film_id','id');
    }

    public function subtitles()
    {
        return $this->hasMany(EpisodeSubtitle::class);
    }

    public function getIsSubtitledAttribute()
    {
        return $this->subtitles()->exists();
    }

    public function getPosterImageAttribute()
    {
        return $this->poster ? $this->getSignedUrl($this->poster) : '';
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
        $activity->causer_id        = Auth::user()->id ?? null;
    }
}
