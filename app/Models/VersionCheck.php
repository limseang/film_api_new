<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

class VersionCheck extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'version',
        'platform',
        'status',
    ];

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
        $activity->default_field    = "{$this->platform} {$this->version}";
        $activity->log_name         = $this->table;
        $activity->causer_id        = Auth::user()->id;
    }
}
