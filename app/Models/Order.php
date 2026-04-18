<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    // Laravel mặc định tìm created_at và updated_at. 
    // Nếu DB của bạn chỉ có created_at, hãy thêm dòng dưới:
    public $timestamps = false; 

    protected $fillable = [
        'user_id', 'total_amount', 'status', 'shipping_address',
        'payment_method', 'shipping_fee', 'discount_amount',
        'coupon_code', 'notes', 'delivered_at', 'tracking_number'
    ];

    // Quan hệ: 1 Đơn hàng có nhiều Chi tiết đơn hàng
    public function orderDetails() {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }
}