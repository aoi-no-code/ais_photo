<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStylePreference extends Model
{
    protected $fillable = ['user_id', 'style_id', 'category_id'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function style()
    {
        return $this->belongsTo(Style::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}