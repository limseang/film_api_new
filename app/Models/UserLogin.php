<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'token',
        'device_id',
        'role_id',
        'device_name',
        'device_os',
        'device_os_version',
        'fcm_token',
        'ip_address',
        'notification_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
