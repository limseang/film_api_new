<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gift extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'id',
        'name',
        'description',
        'image',
        'code',
        'noted',
        'point',
        'quantity',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'expired_date'

    ];
}


