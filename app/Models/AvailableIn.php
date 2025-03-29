<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;
use App\Traits\AlibabaStorage;

class AvailableIn extends Model
{
    use HasFactory , SoftDeletes, LogsActivity, AlibabaStorage;
    protected $fillable = [
        'name',
        'logo',
        'url',
        'type',
    ];

    protected $appends = [
        'image_url'
    ];

    public function getImageUrlAttribute()
    {
        return $this->logo ? $this->getSignedUrl($this->logo) : null;
    }

    public function cinemaBranches()
    {
        return $this->hasMany(CinemBranch::class, 'cinema_id', 'id');
    }

    public function filmAvailables()
    {
        return $this->hasMany(FilmAvailable::class, 'available_id', 'id');
    }
    // belongs to many
    public function films()
    {
        return $this->belongsToMany(Film::class, 'film_availables', 'available_id', 'film_id');
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
        $activity->default_field    = "{$this->name} {$this->url}";
        $activity->log_name         = $this->table;
        $activity->causer_id        = Auth::user()->id;
    }
}
