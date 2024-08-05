<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AlibabaStorage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

class Director extends Model
{
    use HasFactory, AlibabaStorage, LogsActivity;
    protected $fillable = [
        'name',
        'birth_date',
        'death_date',
        'biography',
        'known_for',
        'avatar',
        'know_for',
        'nationality',
        'status'
    ];

    protected $appends = [
        'avatar_url',
        'nationality_name'
    ];
    public function country(){

        return $this->belongsTo(Country::class, 'nationality');

    }

    public function films(){

        return $this->hasMany(Film::class);

    }

    public function getAvatarUrlAttribute(){

        return $this->avatar ? $this->getSignedUrl($this->avatar) : null;
    }

    public function getNationalityNameAttribute(){

        return $this->country ? $this->country->name : null;
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
