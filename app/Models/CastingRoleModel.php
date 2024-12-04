<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastingRoleModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'gender',
        'casting_id',

        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function castingModel()
    {
        return $this->belongsTo(CastingModel::class, 'casting_id', 'id');
    }
}
