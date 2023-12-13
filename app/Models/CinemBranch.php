<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CinemBranch extends Model
{
    use HasFactory;
protected $fillable = [
        'id',
        'cinema_id',
        'name',
        'address',
        'phone',
        'link',
        'show_type',
        'email',
        'facebook',
        'instagram',
        'youtube',
        'image',
        'status'
    ];

    public function cinemas()
    {
        return $this->belongsTo(AvailableIn::class, 'cinema_id', 'id');
    }
}


