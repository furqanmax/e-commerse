<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Shopper\Core\Models\Order;

class OrderConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmed - '.$this->order->number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order.confirmed',
            with: [
                'order' => $this->order,
                'customerName' => $this->order->customer->full_name ?? 'Valued Customer',
                'orderNumber' => $this->order->number,
                'orderTotal' => number_format($this->order->price_amount / 100, 2),
                'items' => $this->order->items,
                'estimatedDelivery' => now()->addDays(5)->format('M j, Y'),
            ],
        );
    }
}
