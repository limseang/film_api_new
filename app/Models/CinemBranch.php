<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AlibabaStorage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

class CinemBranch extends Model
{
    use HasFactory, AlibabaStorage, LogsActivity;
protected $fillable = [
        'id',
        'cinema_id',
        'name',
        'address',
        'phone',
        'link',
        'show_type',
        'email',
        'facebook',
        'instagram',
        'youtube',
        'image',
        'status'
    ];
    protected $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? $this->getSignedUrl($this->image) : null;
    }


    public function cinemas()
    {
        return $this->belongsTo(AvailableIn::class, 'cinema_id', 'id');
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


