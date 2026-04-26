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

class OrderPaymentFailed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly string $failureMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Failed - Order '.$this->order->number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order.payment-failed',
            with: [
                'order' => $this->order,
                'customerName' => $this->order->customer->full_name ?? 'Valued Customer',
                'orderNumber' => $this->order->number,
                'failureMessage' => $this->failureMessage,
            ],
        );
    }
}
