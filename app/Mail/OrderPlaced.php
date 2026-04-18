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
        return $this->subject('Xác nhận đặt hàng #' . $this->order->order_id)
                    ->view('emails.order_master')
                    ->with([
                        'title' => 'Cảm ơn bạn đã đặt hàng!',
                        'content' => 'Đơn hàng',
                        'content_suffix' => 'của bạn đã được hệ thống ghi nhận thành công.'
                    ]);
    }
}