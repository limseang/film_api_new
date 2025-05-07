<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'comment',
        'user_id',
        'item_id',
        'type',
        'report_id',
        'reply_id',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function artical()
    {
        return $this->belongsTo(Artical::class);
    }
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
    public function reply()
    {
        return $this->hasMany(ReplyComment::class);
    }
    public function films ()
    {
        return $this->belongsTo(Film::class);
    }
}
