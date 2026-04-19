<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderAddressUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $order; // Khai báo biến order để dùng ngoài View

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo: Cập nhật địa chỉ đơn hàng #' . $this->order->order_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_address_updated', // Đảm bảo đúng đường dẫn file view
        );
    }

    public function attachments(): array
    {
        return [];
    }
}