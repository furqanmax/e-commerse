<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\OrderConfirmed;
use App\Mail\OrderPaymentFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Shopper\Core\Enum\OrderStatus;
use Shopper\Core\Enum\PaymentStatus;
use Shopper\Core\Models\Order;

class ProcessPaymentJob implements ShouldQueue
{
    use Queueable;

    public string $stripeEventId;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly array $eventData,
    ) {
        $this->stripeEventId = $eventData['id'] ?? '';
        $this->onQueue('payments');
    }

    public function handle(): void
    {
        $eventType = $this->eventData['type'] ?? null;
        $paymentIntentId = $this->eventData['data']['object']['id'] ?? null;

        if (! $eventType || ! $paymentIntentId) {
            Log::warning('ProcessPaymentJob received invalid event data', [
                'event_data' => $this->eventData,
            ]);

            return;
        }

        Log::info('Processing Stripe webhook event', [
            'event_type' => $eventType,
            'payment_intent_id' => $paymentIntentId,
            'stripe_event_id' => $this->stripeEventId,
        ]);

        match ($eventType) {
            'payment_intent.succeeded' => $this->handlePaymentSucceeded($paymentIntentId),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($paymentIntentId),
            'payment_intent.canceled' => $this->handlePaymentCanceled($paymentIntentId),
            default => Log::info('Unhandled Stripe event type', ['event_type' => $eventType]),
        };
    }

    protected function handlePaymentSucceeded(string $paymentIntentId): void
    {
        $order = $this->findOrderByPaymentIntent($paymentIntentId);

        if (! $order) {
            Log::info('Order not found for PaymentIntent, may already be processed', [
                'payment_intent_id' => $paymentIntentId,
            ]);

            return;
        }

        if ($order->payment_status === PaymentStatus::Paid) {
            Log::info('Order already marked as paid', [
                'order_number' => $order->number,
                'payment_intent_id' => $paymentIntentId,
            ]);

            return;
        }

        DB::transaction(function () use ($order, $paymentIntentId) {
            $order->update([
                'payment_status' => PaymentStatus::Paid,
                'status' => OrderStatus::Processing,
                'metadata' => json_encode([
                    ...json_decode($order->metadata ?? '{}', true),
                    'payment_intent_id' => $paymentIntentId,
                    'paid_at' => now()->toIso8601String(),
                ]),
            ]);

            $customer = $order->customer;

            if ($customer && $customer->email) {
                Mail::to($customer->email)->queue(new OrderConfirmed($order));

                Log::info('Order confirmation email queued', [
                    'order_number' => $order->number,
                    'email' => $customer->email,
                ]);
            }
        });

        Log::info('Order marked as paid after webhook', [
            'order_number' => $order->number,
            'payment_intent_id' => $paymentIntentId,
        ]);
    }

    protected function handlePaymentFailed(string $paymentIntentId): void
    {
        $order = $this->findOrderByPaymentIntent($paymentIntentId);

        if (! $order) {
            Log::info('Order not found for failed PaymentIntent', [
                'payment_intent_id' => $paymentIntentId,
            ]);

            return;
        }

        $failureMessage = $this->eventData['data']['object']['last_payment_error']['message']
            ?? 'Payment was declined. Please try again or use a different payment method.';

        DB::transaction(function () use ($order, $failureMessage, $paymentIntentId) {
            $order->update([
                'payment_status' => PaymentStatus::Refunded,
                'status' => OrderStatus::Cancelled,
                'metadata' => json_encode([
                    ...json_decode($order->metadata ?? '{}', true),
                    'payment_intent_id' => $paymentIntentId,
                    'payment_failed_at' => now()->toIso8601String(),
                    'failure_message' => $failureMessage,
                ]),
            ]);

            $customer = $order->customer;

            if ($customer && $customer->email) {
                Mail::to($customer->email)->queue(new OrderPaymentFailed($order, $failureMessage));

                Log::info('Payment failure notification email queued', [
                    'order_number' => $order->number,
                    'email' => $customer->email,
                ]);
            }
        });

        Log::info('Order marked as failed after webhook', [
            'order_number' => $order->number,
            'payment_intent_id' => $paymentIntentId,
            'failure_message' => $failureMessage,
        ]);
    }

    protected function handlePaymentCanceled(string $paymentIntentId): void
    {
        $order = $this->findOrderByPaymentIntent($paymentIntentId);

        if (! $order) {
            return;
        }

        DB::transaction(function () use ($order, $paymentIntentId) {
            $order->update([
                'payment_status' => PaymentStatus::Voided,
                'status' => OrderStatus::Cancelled,
                'metadata' => json_encode([
                    ...json_decode($order->metadata ?? '{}', true),
                    'payment_intent_id' => $paymentIntentId,
                    'canceled_at' => now()->toIso8601String(),
                ]),
            ]);
        });

        Log::info('Order canceled after PaymentIntent cancellation', [
            'order_number' => $order->number,
            'payment_intent_id' => $paymentIntentId,
        ]);
    }

    protected function findOrderByPaymentIntent(string $paymentIntentId): ?Order
    {
        return Order::where('metadata', 'like', '%"payment_intent_id":"'.$paymentIntentId.'"%')
            ->orWhere('metadata', 'like', '%"payment_intent_id":"pi_'.substr($paymentIntentId, 3).'"%')
            ->first();
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessPaymentJob failed permanently', [
            'stripe_event_id' => $this->stripeEventId,
            'event_type' => $this->eventData['type'] ?? 'unknown',
            'exception' => $exception->getMessage(),
        ]);
    }
}
