<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertis extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'link',
        'payment_by',
        'payment_status',
        'payment_date',
        'accept_date',
        'receipt',
        'accept_by',
        'come_from',
        'expire_date',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function adsPlace()
    {
        return $this->belongsTo(AdsPlace::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
