<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RendomPoint extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'id',
        'user_id',
        'gift_id',
        'code',
        'point',
        'status',
        'phone_number',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function users()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function gifts()
    {
        return $this->belongsTo(Gift::class,'gift_id','id');
    }
}
