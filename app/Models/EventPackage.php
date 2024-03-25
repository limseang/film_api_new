<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPackage extends Model
{
    use HasFactory;
    protected $fillable = [
            'name',
            'description',
            'event_id',
            'price',
            'quantity',
            'image',
            'status',
    ];
    public function event()
    {
        return $this->belongsTo(EventPlan::class);
    }
    public function tickets()
    {
        return $this->hasMany(EventTicket::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function items()
    {
        return $this->hasMany(PackageItem::class, 'package_id', 'id');
    }



}
