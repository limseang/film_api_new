<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
//sof delete
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use App\Traits\AlibabaStorage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, AlibabaStorage, LogsActivity;


    protected $fillable = [
        'name',
        'email',
        'role_id',
        'language',
        'phone',
        // 'telegram',
        'avatar',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password',
        // 'role_id',
        'remember_token',
    ];

    protected $appends = [
        'avatar_url',
        'role_name',
        ''
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',

    ];

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    //1 user has 1 role

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function like(){
        return $this->hasMany(Like::class);
    }

    public function Usertype (){
        return $this->belongsTo(UserType::class, 'user_type','id');
    }

    public function UserPremium(){
        return $this->hasOne(PremiumUser::class,'user_id','id');
    }

    public function getAvatarUrlAttribute()
    {
        $social = ['Google', 'Apple', 'Facebook', 'Phone'];
        if (in_array($this->comeFrom, $social)) {
            return $this->avatar;
        }
        return $this->avatar ? $this->getSignedUrl($this->avatar) : URL('img/no_image.png');
    }

    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->name : null;
    }

    public function getUserTypeNameAttribute()
    {
        return $this->Usertype ? $this->Usertype->name : '';
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
        $activity->causer_id        = Auth::user()->id ?? null;
    }

}
