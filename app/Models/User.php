<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'plan_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guarded = [
        'is_admin', // これにより、大量代入を通じてis_adminを設定することはできなくなります。
    ];

// User モデル内のメソッド
public function stylePreferences()
{
    return $this->hasMany(UserStylePreference::class, 'user_id', 'id');
}

// このメソッドのテーブル名を修正
public function categoryPreferences()
{
    // 'user_category_preferences' から 'user_style_preferences' に変更
    return $this->belongsToMany(Category::class, 'user_style_preferences', 'user_id', 'category_id')
                ->withPivot('style_id');
}

    
}
