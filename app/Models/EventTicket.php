<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    use HasFactory;
    protected $fillable = [
            'name',
            'description',
            'price',
            'quantity',
            'status',
            'event_id',
    ];
    public function event()
    {
        return $this->belongsTo(EventPlan::class, 'event_id', 'id');
    }
    public function items()
    {
        return $this->hasMany(PackageItem::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
