<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageItem extends Model
{
    use HasFactory;
    protected $fillable = [
            'item_id',
            'package_id',
            'quantity',
            'status',
    ];
    public function item()
    {
        return $this->belongsTo(EventItem::class, 'item_id', 'id');
    }
    public function package()
    {
        return $this->belongsTo(EventPackage::class, 'package_id', 'id');
    }

}
