<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Các trường có thể gán (fillable) để hỗ trợ thêm thông tin người dùng.
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'gmail', 'address','google_id','role',
    ];
    /**
     * Các trường ẩn khi trả về JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Định dạng kiểu dữ liệu của các trường.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Tạo token API mới cho người dùng.
     *
     * @return string
     */
    public function generateToken()
    {
        return $this->createToken('API Token')->plainTextToken;
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
