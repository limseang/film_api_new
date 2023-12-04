<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookMark extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'post_id',
        'post_type',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function artical()
    {
        return $this->belongsTo(Artical::class);
    }
    public function film()
    {
        return $this->belongsTo(Film::class);
    }
}
