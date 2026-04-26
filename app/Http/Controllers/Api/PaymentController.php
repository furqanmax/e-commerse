<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPaymentJob;
use App\Services\CartService;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Shopper\Core\Enum\OrderStatus;
use Shopper\Core\Enum\PaymentStatus;
use Shopper\Core\Enum\ShippingStatus;
use Shopper\Core\Models\Address;
use Shopper\Core\Models\Order;
use Shopper\Core\Models\OrderItem;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function __construct(
        private readonly StripeService $stripeService,
        private readonly CartService $cartService,
    ) {}

    public function createIntent(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'checkout_session_id' => ['required', 'string', 'uuid'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $storedSession = $request->session()->get('checkout_session');

        if (! $storedSession || $storedSession['session_id'] !== $request->checkout_session_id) {
            return response()->json([
                'message' => 'Invalid or expired checkout session.',
                'errors' => ['checkout_session_id' => ['Session not found or expired.']],
            ], 422);
        }

        $user = $request->user();

        if ($storedSession['user_id'] !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized.',
                'errors' => ['checkout_session_id' => ['Session does not belong to this user.']],
            ], 403);
        }

        try {
            $total = $this->calculateTotal($storedSession);

            if ($total <= 0) {
                return response()->json([
                    'message' => 'Invalid order amount.',
                    'errors' => ['amount' => ['Order amount must be greater than zero.']],
                ], 422);
            }

            $amountInCents = (int) ($total * 100);
            $idempotencyKey = 'order_'.$request->checkout_session_id;

            $paymentIntent = $this->stripeService->createPaymentIntent(
                amount: $amountInCents,
                currency: 'usd',
                idempotencyKey: $idempotencyKey,
                metadata: [
                    'checkout_session_id' => $request->checkout_session_id,
                    'user_id' => $user->id,
                ]
            );

            $request->session()->put('stripe_payment_intent_id', $paymentIntent->id);

            Log::info('Stripe PaymentIntent created', [
                'payment_intent_id' => $paymentIntent->id,
                'checkout_session_id' => $request->checkout_session_id,
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'publishable_key' => config('services.stripe.publishable_key'),
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amountInCents,
                'currency' => 'usd',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create PaymentIntent', [
                'checkout_session_id' => $request->checkout_session_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to initialize payment. Please try again.',
                'errors' => ['payment' => [$e->getMessage()]],
            ], 500);
        }
    }

    public function confirm(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'checkout_session_id' => ['required', 'string', 'uuid'],
            'payment_intent_id' => ['required', 'string'],
            'payment_method_id' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $storedSession = $request->session()->get('checkout_session');

        if (! $storedSession || $storedSession['session_id'] !== $request->checkout_session_id) {
            return response()->json([
                'message' => 'Invalid or expired checkout session.',
                'errors' => ['checkout_session_id' => ['Session not found or expired.']],
            ], 422);
        }

        $user = $request->user();

        if ($storedSession['user_id'] !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized.',
                'errors' => ['checkout_session_id' => ['Session does not belong to this user.']],
            ], 403);
        }

        try {
            if ($request->payment_method_id) {
                $this->stripeService->attachPaymentMethod(
                    $request->payment_intent_id,
                    $request->payment_method_id
                );
            }

            $paymentIntent = $this->stripeService->confirmPaymentIntent($request->payment_intent_id);

            Log::info('Stripe PaymentIntent confirmed', [
                'payment_intent_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
            ]);

            if (in_array($paymentIntent->status, ['succeeded', 'requires_capture'])) {
                $order = $this->createOrder($user, $storedSession, $paymentIntent->id);

                $request->session()->forget('checkout_session');
                $request->session()->forget('stripe_payment_intent_id');

                $estimatedDelivery = now()->addDays(5)->format('M j, Y');

                return response()->json([
                    'order_number' => $order->number,
                    'status' => $order->status->value,
                    'payment_status' => $order->payment_status->value,
                    'total' => (float) ($order->price_amount / 100),
                    'estimated_delivery' => $estimatedDelivery,
                    'message' => 'Order placed successfully.',
                ], 201);
            }

            return response()->json([
                'status' => $paymentIntent->status,
                'message' => 'Payment requires additional action.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Payment confirmation failed', [
                'checkout_session_id' => $request->checkout_session_id,
                'payment_intent_id' => $request->payment_intent_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Payment failed. Please try again or use a different payment method.',
                'errors' => ['payment' => [$e->getMessage()]],
            ], 422);
        }
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        if (! $sigHeader || ! $webhookSecret) {
            Log::warning('Stripe webhook received without signature or secret');

            return response()->json(['error' => 'Missing signature'], 400);
        }

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe webhook received', [
            'event_type' => $event->type,
            'event_id' => $event->id,
        ]);

        try {
            ProcessPaymentJob::dispatch($event->toArray());

            return response()->json(['received' => true]);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch payment job', [
                'event_type' => $event->type,
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['received' => true]);
        }
    }

    protected function calculateTotal(array $session): float
    {
        $this->cartService->setUser(auth()->user());
        $this->cartService->resolveCart();
        $cart = $this->cartService->getCart();

        $subtotal = $cart['subtotal'] ?? 0;
        $discount = $cart['discount'] ?? 0;
        $shipping = $session['shipping_cost'] ?? 0;
        $tax = $session['tax'] ?? 0;

        return round($subtotal - $discount + $shipping + $tax, 2);
    }

    protected function createOrder(
        $user,
        array $session,
        string $paymentIntentId
    ): Order {
        return DB::transaction(function () use ($user, $session, $paymentIntentId) {
            $this->cartService->setUser($user);
            $this->cartService->resolveCart();
            $cart = $this->cartService->getCart();

            if (empty($cart['items'])) {
                throw new \Exception('Cart is empty.');
            }

            $address = Address::find($session['shipping_address_id']);

            if (! $address || $address->user_id !== $user->id) {
                throw new \Exception('Invalid shipping address.');
            }

            $subtotal = $cart['subtotal'];
            $discount = $cart['discount'];
            $shipping = $session['shipping_cost'];
            $tax = $session['tax'];
            $total = $subtotal - $discount + $shipping + $tax;

            $order = Order::create([
                'customer_id' => $user->id,
                'channel_id' => 1,
                'shipping_address_id' => $address->id,
                'billing_address_id' => $address->id,
                'status' => OrderStatus::New,
                'payment_status' => PaymentStatus::Paid,
                'shipping_status' => ShippingStatus::Unfulfilled,
                'price_amount' => (int) ($total * 100),
                'currency_code' => 'USD',
                'tax_amount' => (int) ($tax * 100),
                'number' => $this->generateOrderNumber(),
                'metadata' => json_encode([
                    'payment_method' => 'stripe',
                    'payment_intent_id' => $paymentIntentId,
                    'coupon_code' => $cart['coupon_code'] ?? null,
                ]),
            ]);

            foreach ($cart['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'] ?? null,
                    'name' => $item['product_name'] ?? 'Product',
                    'quantity' => $item['quantity'],
                    'unit_price_amount' => (int) (($item['unit_price'] ?? 0) * 100),
                ]);

                $this->decrementStock(
                    $item['product_id'] ?? null,
                    $item['variant_id'] ?? null,
                    $item['quantity']
                );
            }

            DB::table('sh_carts')
                ->where('customer_id', $user->id)
                ->whereNull('completed_at')
                ->update(['completed_at' => now()]);

            return $order;
        });
    }

    protected function decrementStock(?int $productId, ?int $variantId, int $quantity): void
    {
        if (! empty($variantId)) {
            DB::table('sh_product_variants')
                ->where('id', $variantId)
                ->decrement('stock', $quantity);
        } elseif (! empty($productId)) {
            DB::table('sh_products')
                ->where('id', $productId)
                ->decrement('stock', $quantity);
        }
    }

    protected function generateOrderNumber(): string
    {
        $prefix = config('shopper.orders.number_prefix', 'ORD');
        $number = str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);

        return $prefix.'-'.date('Ymd').'-'.$number;
    }
}
