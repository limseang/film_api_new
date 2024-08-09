<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AlibabaStorage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

class Genre extends Model
{
    use HasFactory, AlibabaStorage, LogsActivity;
    protected $fillable = [
        'name',
        'description',
        'image',
        'status'
    ];

    protected $appends = [
        'image_url',
    ];
    public function films()
    {
        return $this->hasMany(Film::class);
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
        $activity->default_field    = "{$this->name}";
        $activity->log_name         = $this->table;
        $activity->causer_id        = Auth::user()->id;
    }
}
