<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestFilm extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'film_name',
        'film_link',
        'film_image',
        'film_description',
        'noted',
        'status'
    ];
    
    /**
     * Get the user that made the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 1:
                return 'Pending';
            case 2:
                return 'Completed';
            case 3:
                return 'Rejected';
            default:
                return 'Unknown';
        }
    }
}
