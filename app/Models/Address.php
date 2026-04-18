<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';
    protected $primaryKey = 'address_id';
    public $timestamps = false; // Vì ông chỉ dùng created_at thủ công

    protected $fillable = [
        'user_id', 'fullname', 'phone', 'city', 'district', 'ward', 'street', 'is_default', 'created_at'
    ];
}