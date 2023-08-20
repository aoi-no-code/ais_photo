<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function images()
    {
        return $this->belongsToMany(Image::class);
    }
    
    public function style()
    {
        return $this->belongsTo(Style::class);
    }
}
