<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function build()
    {
        // Lấy tên người dùng từ quan hệ của Order thay vì dùng Auth::user()
        // Giả sử Model Order đã có: public function user() { return $this->belongsTo(User::class, 'user_id'); }
        $customerName = $this->order->user->fullname ?? 'Khách hàng';

        return $this->subject('Xác nhận đặt hàng #' . $this->order->order_id)
                    ->view('emails.order')
                    ->with([
                        'title' => 'Cảm ơn bạn đã đặt hàng!',
                        'content' => 'Đơn hàng',
                        'content_suffix' => 'của bạn đã được hệ thống ghi nhận thành công.',
                        'userName' => $customerName // Truyền biến này qua
                    ]);
    }
}