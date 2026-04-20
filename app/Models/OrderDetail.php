<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_items'; // Khai báo đúng tên bảng thực tế
    protected $primaryKey = 'order_item_id';
    public $timestamps = false; // Bảng này thường không cần created_at riêng

    protected $fillable = [
        'order_id', 'book_id', 'quantity', 'price'
    ];

    // Quan hệ: 1 Chi tiết đơn hàng thuộc về 1 Cuốn sách
    public function book() {
        return $this->belongsTo(Book::class, 'book_id', 'book_id');
    }

    // Quan hệ ngược: Thuộc về 1 Đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}