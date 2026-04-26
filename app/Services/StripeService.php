<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Service\PaymentIntentService;
use Stripe\StripeClient;

class StripeService
{
    protected StripeClient $client;

    protected PaymentIntentService $paymentIntents;

    public function __construct()
    {
        $secretKey = config('services.stripe.secret_key');

        if (! $secretKey) {
            throw new \RuntimeException('Stripe secret key is not configured.');
        }

        $this->client = new StripeClient($secretKey);
        $this->paymentIntents = $this->client->paymentIntents;
    }

    public function createPaymentIntent(
        int $amount,
        string $currency = 'usd',
        ?string $idempotencyKey = null,
        array $metadata = [],
        ?string $captureMethod = null,
    ): PaymentIntent {
        $params = [
            'amount' => $amount,
            'currency' => $currency,
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
            'metadata' => $metadata,
        ];

        if ($captureMethod) {
            $params['capture_method'] = $captureMethod;
        } else {
            $params['capture_method'] = config('services.stripe.capture_method', 'automatic');
        }

        if ($idempotencyKey) {
            $params['idempotencyKey'] = $idempotencyKey;
        }

        try {
            return $this->paymentIntents->create($params);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error creating PaymentIntent', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'currency' => $currency,
            ]);

            throw $e;
        }
    }

    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return $this->paymentIntents->retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error retrieving PaymentIntent', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId,
            ]);

            throw $e;
        }
    }

    public function confirmPaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return $this->paymentIntents->confirm($paymentIntentId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error confirming PaymentIntent', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId,
            ]);

            throw $e;
        }
    }

    public function attachPaymentMethod(string $paymentIntentId, string $paymentMethodId): PaymentIntent
    {
        try {
            $this->client->paymentMethods->attach($paymentMethodId, [
                'customer' => $this->getCustomerId(),
            ]);

            return $this->paymentIntents->update($paymentIntentId, [
                'payment_method' => $paymentMethodId,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error attaching payment method', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId,
                'payment_method_id' => $paymentMethodId,
            ]);

            throw $e;
        }
    }

    public function capturePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return $this->paymentIntents->capture($paymentIntentId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error capturing PaymentIntent', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId,
            ]);

            throw $e;
        }
    }

    public function cancelPaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return $this->paymentIntents->cancel($paymentIntentId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe API error canceling PaymentIntent', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId,
            ]);

            throw $e;
        }
    }

    protected function getCustomerId(): ?string
    {
        return auth()->user()?->stripe_customer_id;
    }
}
