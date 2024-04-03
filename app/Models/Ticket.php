<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickes';

    protected $fillable = [
        'name',
        'row',
        'seat',
        'code',
        'image'
    ];
}
