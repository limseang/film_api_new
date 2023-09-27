<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryArtical extends Model
{
    use HasFactory;
    protected $fillable = [
      'category_id',
        'artical_id'
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }

    public function articals()
    {
        return $this->belongsTo(Artical::class,'artical_id','id');
    }
}
