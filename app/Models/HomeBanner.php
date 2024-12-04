<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'item_type',
        'item_id',
        'image',
        'link',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ads()
    {
        return $this->belongsTo(Advertis::class, 'item_id', 'id');

    }

    public function artical()
    {
        return $this->belongsTo(Artical::class, 'item_id', 'id');

    }

    public function films()
    {
        return $this->belongsTo(Film::class, 'item_id', 'id');

    }

    public function casting()
    {
        return $this->belongsTo(CastingModel::class, 'item_id', 'id');

    }
}
