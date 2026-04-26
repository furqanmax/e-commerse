<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Shopper\Core\Enum\OrderStatus;
use Shopper\Core\Enum\PaymentStatus;
use Shopper\Core\Enum\ShippingStatus;
use Shopper\Core\Models\Address;
use Shopper\Core\Models\Order;
use Shopper\Core\Models\OrderItem;

class CheckoutController extends Controller
{
    private const FREE_SHIPPING_THRESHOLD = 5000;

    public function __construct(
        private readonly CartService $cartService,
    ) {}

    public function session(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'shipping_address_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $address = Address::find($request->shipping_address_id);

        if (! $address || $address->user_id !== $user->id) {
            return response()->json([
                'message' => 'Invalid shipping address.',
                'errors' => ['shipping_address_id' => ['Address not found or unauthorized.']],
            ], 422);
        }

        $this->cartService->setUser($user);
        $this->cartService->resolveCart();

        $cart = $this->cartService->getCart();

        if (empty($cart['items'])) {
            return response()->json([
                'message' => 'Cart is empty.',
                'errors' => ['cart' => ['Your cart is empty. Add items before checkout.']],
            ], 422);
        }

        $outOfStockItems = $this->checkStockLevels($cart['items']);

        if (! empty($outOfStockItems)) {
            return response()->json([
                'message' => 'Some items are out of stock.',
                'errors' => ['cart' => ['One or more items in your cart are no longer available.']],
                'out_of_stock_items' => $outOfStockItems,
            ], 422);
        }

        $sessionId = Str::uuid()->toString();
        $shippingMethods = $this->getAvailableShippingMethods($cart['subtotal']);
        $selectedShipping = $this->calculateShipping($request->shipping_method_id, $cart['subtotal']);

        $tax = $this->calculateTax($cart['subtotal']);

        $session = [
            'session_id' => $sessionId,
            'shipping_address' => $this->formatAddress($address),
            'items_summary' => $this->formatItemsSummary($cart['items']),
            'subtotal' => $cart['subtotal'],
            'shipping' => $selectedShipping,
            'discount' => $cart['discount'],
            'tax' => $tax,
            'total' => $cart['subtotal'] - $cart['discount'] + $selectedShipping + $tax,
            'available_shipping_methods' => $shippingMethods,
            'payment_methods' => $this->getPaymentMethods(),
        ];

        $request->session()->put('checkout_session', [
            'session_id' => $sessionId,
            'user_id' => $user->id,
            'shipping_address_id' => $address->id,
            'shipping_method_id' => $request->shipping_method_id,
            'shipping_cost' => $selectedShipping,
            'tax' => $tax,
            'created_at' => now()->toIso8601String(),
        ]);

        return response()->json($session);
    }

    public function confirm(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => ['required', 'string', 'uuid'],
            'payment_method' => ['required', 'string', 'in:stripe,cod,bank_transfer'],
            'payment_token' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $storedSession = $request->session()->get('checkout_session');

        if (! $storedSession || $storedSession['session_id'] !== $request->session_id) {
            return response()->json([
                'message' => 'Invalid or expired checkout session.',
                'errors' => ['session_id' => ['Session not found or expired.']],
            ], 422);
        }

        if ($storedSession['user_id'] !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized.',
                'errors' => ['session_id' => ['Session does not belong to this user.']],
            ], 403);
        }

        $user = $request->user();

        try {
            $order = DB::transaction(function () use ($request, $user, $storedSession) {
                $this->cartService->setUser($user);
                $this->cartService->resolveCart();

                $cart = $this->cartService->getCart();

                if (empty($cart['items'])) {
                    throw new \Exception('Cart is empty.');
                }

                $outOfStockItems = $this->checkStockLevels($cart['items']);

                if (! empty($outOfStockItems)) {
                    throw new \Exception('One or more items are no longer available.');
                }

                $address = Address::find($storedSession['shipping_address_id']);

                if (! $address || $address->user_id !== $user->id) {
                    throw new \Exception('Invalid shipping address.');
                }

                $subtotal = $cart['subtotal'];
                $discount = $cart['discount'];
                $shipping = $storedSession['shipping_cost'];
                $tax = $storedSession['tax'];
                $total = $subtotal - $discount + $shipping + $tax;

                $order = Order::create([
                    'customer_id' => $user->id,
                    'channel_id' => 1,
                    'shipping_address_id' => $address->id,
                    'billing_address_id' => $address->id,
                    'shipping_option_id' => $storedSession['shipping_method_id'],
                    'status' => OrderStatus::New,
                    'payment_status' => PaymentStatus::Pending,
                    'shipping_status' => ShippingStatus::Unfulfilled,
                    'price_amount' => (int) ($total * 100),
                    'currency_code' => 'USD',
                    'tax_amount' => (int) ($tax * 100),
                    'number' => $this->generateOrderNumber(),
                    'metadata' => json_encode([
                        'payment_method' => $request->payment_method,
                        'payment_token' => $request->payment_token,
                        'coupon_code' => $cart['coupon_code'],
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

                if ($cart['coupon_code']) {
                    DB::table('sh_carts')
                        ->where('customer_id', $user->id)
                        ->whereNull('completed_at')
                        ->update(['coupon_code' => null]);
                }

                return $order;
            });

            $request->session()->forget('checkout_session');

            $estimatedDelivery = now()->addDays(5)->format('M j, Y');

            return response()->json([
                'order_number' => $order->number,
                'status' => $order->status->value,
                'total' => (float) ($order->price_amount / 100),
                'estimated_delivery' => $estimatedDelivery,
                'message' => 'Order placed successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Checkout failed: '.$e->getMessage(),
                'errors' => ['checkout' => [$e->getMessage()]],
            ], 422);
        }
    }

    protected function checkStockLevels(array $items): array
    {
        $outOfStock = [];

        foreach ($items as $item) {
            $stockQty = $this->getStockQuantity($item);

            if ($stockQty < ($item['quantity'] ?? 1)) {
                $outOfStock[] = [
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'] ?? 'Unknown Product',
                    'requested_quantity' => $item['quantity'] ?? 1,
                    'available_quantity' => $stockQty,
                ];
            }
        }

        return $outOfStock;
    }

    protected function getStockQuantity(array $item): int
    {
        if (! empty($item['variant_id'])) {
            $variant = DB::table('sh_product_variants')
                ->where('id', $item['variant_id'])
                ->first();

            return (int) ($variant?->stock ?? 0);
        }

        $product = DB::table('sh_products')
            ->where('id', $item['product_id'] ?? null)
            ->first();

        return (int) ($product?->stock ?? 0);
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

    protected function getAvailableShippingMethods(float $subtotal): array
    {
        $freeShippingEligible = $subtotal * 100 >= self::FREE_SHIPPING_THRESHOLD;

        $methods = [
            [
                'id' => 'standard',
                'name' => 'Standard Shipping',
                'description' => 'Delivery in 5-7 business days',
                'price' => $freeShippingEligible ? 0 : 5.99,
                'estimated_days' => '5-7',
            ],
            [
                'id' => 'express',
                'name' => 'Express Shipping',
                'description' => 'Delivery in 2-3 business days',
                'price' => 12.99,
                'estimated_days' => '2-3',
            ],
            [
                'id' => 'overnight',
                'name' => 'Overnight Shipping',
                'description' => 'Next business day delivery',
                'price' => 24.99,
                'estimated_days' => '1',
            ],
        ];

        return $methods;
    }

    protected function calculateShipping(?string $methodId, float $subtotal): float
    {
        $freeShippingEligible = $subtotal * 100 >= self::FREE_SHIPPING_THRESHOLD;

        return match ($methodId) {
            'standard' => $freeShippingEligible ? 0.0 : 5.99,
            'express' => 12.99,
            'overnight' => 24.99,
            default => $freeShippingEligible ? 0.0 : 5.99,
        };
    }

    protected function calculateTax(float $amount): float
    {
        return round($amount * 0.08, 2);
    }

    protected function getPaymentMethods(): array
    {
        return [
            [
                'id' => 'stripe',
                'name' => 'Credit/Debit Card',
                'description' => 'Pay securely with Stripe',
                'icon' => 'credit-card',
            ],
            [
                'id' => 'cod',
                'name' => 'Cash on Delivery',
                'description' => 'Pay when you receive your order',
                'icon' => 'cash',
            ],
            [
                'id' => 'bank_transfer',
                'name' => 'Bank Transfer',
                'description' => 'Pay via bank transfer',
                'icon' => 'bank',
            ],
        ];
    }

    protected function formatAddress(Address $address): array
    {
        $country = $address->country;

        return [
            'id' => $address->id,
            'name' => $address->first_name.' '.$address->last_name,
            'address1' => $address->street_address,
            'address2' => $address->street_address_plus,
            'city' => $address->city,
            'state' => $address->state,
            'postcode' => $address->postal_code,
            'country' => $country?->name ?? '',
            'phone' => $address->phone_number,
        ];
    }

    protected function formatItemsSummary(array $items): array
    {
        return array_map(fn ($item) => [
            'product_id' => $item['product_id'] ?? null,
            'product_name' => $item['product_name'] ?? 'Product',
            'quantity' => $item['quantity'] ?? 1,
            'unit_price' => $item['unit_price'] ?? 0,
            'subtotal' => $item['subtotal'] ?? 0,
        ], $items);
    }

    protected function generateOrderNumber(): string
    {
        $prefix = config('shopper.orders.number_prefix', 'ORD');
        $number = str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);

        return $prefix.'-'.date('Ymd').'-'.$number;
    }
}
