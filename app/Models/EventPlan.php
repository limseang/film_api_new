<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPlan extends Model
{
    use HasFactory;
    protected $fillable = [
            'name',
            'description',
            'location',
            'location_link',
            'start_time',
            'end_time',
            'start_date',
            'status',
            'type',
            'image',
            'ticket_price',
            'ticket_quantity',
            'genre_id',

    ];
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
    public function tickets()
    {
        return $this->hasMany(EventTicket::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function packages()
    {
        return $this->hasMany(EventPackage::class, 'event_id', 'id');
    }

}
