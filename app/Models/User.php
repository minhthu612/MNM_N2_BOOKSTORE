<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    // 🔥 QUAN TRỌNG: khóa chính của bạn là user_id
    protected $primaryKey = 'user_id';

    public $timestamps = false; // nếu DB không có updated_at

    protected $fillable = [
        'username',
        'email',
        'fullname',
        'password_hashed',
        'role',
        'status'
    ];

    protected $hidden = [
        'password_hashed',
        'remember_token',
    ];

    // 🔥 Laravel dùng password → map sang password_hashed
    public function getAuthPassword()
    {
        return $this->password_hashed;
    }
}