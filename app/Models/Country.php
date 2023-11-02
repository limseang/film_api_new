<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'flag',
        'nationality',
        'language',
        'status'
    ];

    public function artists()
    {
        return $this->hasMany(Artist::class);
    }

    public function directors()
    {
        return $this->hasMany(Director::class);
    }


}
