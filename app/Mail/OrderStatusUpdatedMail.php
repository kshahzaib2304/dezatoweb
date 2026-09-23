<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $previousLabel,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order '.$this->order->number.' update: '.$this->order->statusLabel(),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.order-status-updated',
            with: [
                'order' => $this->order,
                'previousLabel' => $this->previousLabel,
            ],
        );
    }
}
