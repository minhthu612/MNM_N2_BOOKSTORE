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
        'password_hashed',
        'PASSWORD',
        'phone',
        'role',
        'status'
    ];

    protected $hidden = [
        'password_hashed',
    ];

    // 🔥 QUAN TRỌNG: Laravel sẽ dùng cái này để check password
    public function getAuthPassword()
    {
        return $this->password_hashed;
    }
}