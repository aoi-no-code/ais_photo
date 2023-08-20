<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['filename', 'download_count', 'category_id'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_image');
    }
}
