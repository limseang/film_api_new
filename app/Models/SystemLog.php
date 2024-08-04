<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;
    use HasFactory;

    protected $table = 'activity_log';

    public function getCreatedAtAttribute()
    {
        return dateTimeFormat($this->attributes['created_at']);
    }
}
