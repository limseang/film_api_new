<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Traits\AlibabaStorage;
use Illuminate\Support\Facades\Auth;

class Artist extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, AlibabaStorage;
    protected $fillable = [
        'id',
        'name',
        'birth_date',
        'death_date',
        'nationality',
        'profile',
        'biography',
        'known_for',
        'status',
        'film'
    ];

    protected $appends =[
        'nationality_name',
        'avatar_url'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class,'nationality','id');
    }

    public function casts()
    {
        return $this->belongsToMany(Film::class,'casts','actor_id','film_id')->whereNull('casts.deleted_at');
    }

    public function films()
    {
        return $this->belongsToMany(Film::class,'casts','actor_id','film_id');
    }
    public function getNationalityNameAttribute()
    {
        return $this->country->name ?? '';

    }

    public function getAvatarUrlAttribute(){

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
