<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'artical_id'

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function artical()
    {
        return $this->belongsTo(Artical::class);
    }
}
