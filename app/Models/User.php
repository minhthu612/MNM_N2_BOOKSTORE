<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'email',
        'fullname',
        'password_hashed', // Cột này ông đang dùng để lưu
        'PASSWORD',        // PHẢI THÊM CỘT NÀY VÀO ĐÂY THÌ NÓ MỚI HẾT NULL
        'phone',
        'role',
        'status'
    ];

    protected $hidden = [
        'password_hashed',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_hashed;
    }
}