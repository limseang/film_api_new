<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storages extends Model
{
    use HasFactory;
    protected $fillable = [
        'path',
        'extension',
        'size',
        'type',
        'is_used',
    ];

}
