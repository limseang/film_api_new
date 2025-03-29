<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdsPlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'value',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ads()
    {
        return $this->hasMany(Advertis::class);
    }
}
