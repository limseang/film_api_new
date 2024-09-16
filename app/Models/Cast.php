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

class Cast extends Model
{
    use HasFactory , SoftDeletes, AlibabaStorage, LogsActivity;
    protected $table='casts';
    protected $fillable = [
        'film_id',
        'actor_id',
        'character',
        'position',
        'image',
        'status'
    ];

    protected $appends = [
        'image_url',
        'film_name',
        'actor_name'
    ];

    public function films()
    {
        return $this->belongsToMany(Film::class,'film_id','id');
    }

    public function film()
    {
        return $this->belongsTo(Film::class,'film_id','id');
    }
    public function artists()
    {
        return $this->belongsTo(Artist::class,'actor_id','id');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? $this->getSignedUrl($this->image) : null;
    }

    public function getFilmNameAttribute()
    {
        return $this->film ? $this->film->title : null;
    }

    public function getActorNameAttribute()
    {
        return $this->artists ? $this->artists->name : null;
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
        $activity->default_field    = "{$this->character}";
        $activity->log_name         = $this->table;
        $activity->causer_id        = Auth::user()->id;
    }

}
