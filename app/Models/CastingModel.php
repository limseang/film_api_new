<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastingModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'poster',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function castingRoleModels()
    {
        return $this->hasMany(CastingRoleModel::class, 'casting_id', 'id');
    }


}
