<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcript extends Model
{
    use HasFactory;

    protected $table = 'subcripts';
    protected $fillable = [
        'name',
        'duration',
        'price',
        'description',
        'supplier_code',
        'uuid',
        'status',

    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Subcript::uuid()->toString(); // Automatically generate UUID
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_code', 'supplier_code');
    }


}
