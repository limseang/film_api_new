<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farvorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_type',
        'item_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function film()
    {
        return $this->hasMany(Film::class, 'item_id', 'id');
    }

    public function article()
    {
        return $this->belongsTo(Artical::class, 'item_id');
    }
}
