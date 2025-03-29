<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';
    protected $fillable = [
        'name',
        'supplier_code',
        'status',
        'description',
        'callback'
    ];

    public function subcripts()
    {
        return $this->hasMany(Subcript::class, 'supplier_code', 'supplier_code');
    }
}
