<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCodeRecord extends Model
{
    use HasFactory;

    protected $table = 'qrcode_records';

    protected $fillable = [
        'code',
        'content'
    ];
}
