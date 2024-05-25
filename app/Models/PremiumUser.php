<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremiumUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'payment_id',
        'register_date',
        'expired_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getRegisterDateAttribute($value)
    {
        return date('d M Y', strtotime($value));
    }
}
