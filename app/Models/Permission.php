<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'parent_id',
        'icon'
    ];

    /**
     * Get the $cast for the permission.
     */
    protected $casts = [
        'id' => 'integer',
        'parent_id' => 'integer',
        'name' => 'string',
        'guard_name' => 'string',
        'description' => 'string',
    ];
   
    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id', 'id');
    }
}
