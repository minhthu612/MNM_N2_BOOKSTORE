<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Gửi đến view emails.order với các câu chữ thông báo hủy
        return $this->subject('Thông báo hủy đơn hàng #' . $this->order->order_id)
                    ->view('emails.order')
                    ->with([
                        'title' => 'ĐƠN HÀNG ĐÃ HỦY',
                        'content' => 'Chúng tôi xác nhận đơn hàng',
                        'content_suffix' => 'đã được hủy thành công theo yêu cầu của bạn.'
                    ]);
    }
}