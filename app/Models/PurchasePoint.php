<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePoint extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'point',
        'price',
        'payment_method',
        'payment_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
