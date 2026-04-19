<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    // app/Mail/OrderCancelled.php
    public function build()
    {
        // Chỉ truyền những câu chữ khác biệt qua thôi, $order tự động có ở View rồi
        return $this->subject('Thông báo hủy đơn hàng #' . $this->order->order_id)
                    ->view('emails.order')
                    ->with([
                        'title' => 'Đơn hàng của bạn đã được hủy!',
                        'content' => 'Chúng tôi xác nhận đơn hàng',
                        'content_suffix' => 'đã được hủy thành công theo yêu cầu.'
                    ]);
    }
}