<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Jobs\ProcessPaymentJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_requires_signature(): void
    {
        $response = $this->postJson('/api/payments/webhook', [
            'type' => 'payment_intent.succeeded',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Missing signature',
            ]);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test_secret']);

        $payload = json_encode([
            'type' => 'payment_intent.succeeded',
            'id' => 'evt_'.Str::random(24),
            'data' => [
                'object' => [
                    'id' => 'pi_'.Str::random(24),
                ],
            ],
        ]);

        $response = $this->call(
            'POST',
            '/api/payments/webhook',
            [],
            [],
            [],
            [
                'HTTP_STRIPE_SIGNATURE' => 'invalid_signature',
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload
        );

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid signature',
            ]);
    }

    public function test_webhook_dispatches_process_payment_job_with_valid_signature(): void
    {
        Queue::fake();

        config(['services.stripe.webhook_secret' => 'whsec_test_secret']);

        $eventId = 'evt_'.Str::random(24);
        $paymentIntentId = 'pi_'.Str::random(24);

        $payload = json_encode([
            'type' => 'payment_intent.succeeded',
            'id' => $eventId,
            'data' => [
                'object' => [
                    'id' => $paymentIntentId,
                ],
            ],
        ]);

        $secret = 'whsec_test_secret';
        $timestamp = time();
        $signedPayload = $timestamp.'.'.$payload;
        $signature = hash_hmac('sha256', $signedPayload, $secret);

        $response = $this->call(
            'POST',
            '/api/payments/webhook',
            [],
            [],
            [],
            [
                'HTTP_STRIPE_SIGNATURE' => 't='.$timestamp.',v1='.$signature,
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload
        );

        $response->assertStatus(200)
            ->assertJson([
                'received' => true,
            ]);

        Queue::assertPushed(ProcessPaymentJob::class);
    }

    public function test_create_payment_intent_requires_authentication(): void
    {
        $response = $this->postJson('/api/payments/intent', [
            'checkout_session_id' => Str::uuid()->toString(),
        ]);

        $response->assertStatus(401);
    }

    public function test_confirm_payment_requires_authentication(): void
    {
        $response = $this->postJson('/api/payments/confirm', [
            'checkout_session_id' => Str::uuid()->toString(),
            'payment_intent_id' => 'pi_'.Str::random(24),
        ]);

        $response->assertStatus(401);
    }
}
