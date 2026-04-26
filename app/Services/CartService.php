<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    private ?int $cartId = null;

    private ?string $guestId = null;

    private ?int $userId = null;

    private ?User $user = null;

    private Collection $items;

    private ?string $couponCode = null;

    private float $discountAmount = 0;

    public function __construct(?User $user = null)
    {
        $this->user = $user;
        $this->userId = $user?->id;
        $this->items = collect();
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        $this->userId = $user?->id;

        return $this;
    }

    public function setGuestId(?string $guestId): self
    {
        $this->guestId = $guestId;

        return $this;
    }

    public function resolveCart(): self
    {
        $cart = $this->findOrCreateCart();

        if ($cart) {
            $this->cartId = $cart->id;
            $this->couponCode = $cart->coupon_code;
            $this->loadItems();
        }

        return $this;
    }

    protected function findOrCreateCart(): ?object
    {
        $query = DB::table('sh_carts')
            ->whereNull('completed_at');

        if ($this->userId) {
            $query->where('customer_id', $this->userId);
        } elseif ($this->guestId) {
            $query->whereJsonContains('metadata', ['guest_id' => $this->guestId]);
        } else {
            return null;
        }

        $cart = $query->first();

        if (! $cart && ($this->userId || $this->guestId)) {
            $cartId = DB::table('sh_carts')->insertGetId([
                'customer_id' => $this->userId,
                'channel_id' => 1,
                'currency_code' => 'USD',
                'metadata' => json_encode(['guest_id' => $this->guestId]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $cart = DB::table('sh_carts')->where('id', $cartId)->first();
        }

        return $cart;
    }

    protected function loadItems(): void
    {
        if (! $this->cartId) {
            return;
        }

        $lines = DB::table('sh_cart_lines')
            ->where('cart_id', $this->cartId)
            ->get();

        $items = [];

        foreach ($lines as $line) {
            $product = DB::table('sh_products')->where('id', $line->purchasable_id)->first();
            $variant = null;

            if ($line->purchasable_type === 'Shopper\Core\Models\ProductVariant') {
                $variant = DB::table('sh_product_variants')->where('id', $line->purchasable_id)->first();
            }

            $price = $line->unit_price_amount / 100;
            $qty = $line->quantity;

            $items[] = (object) [
                'id' => $line->id,
                'product_id' => $product?->id,
                'product_name' => $product?->name,
                'thumbnail' => $this->getProductThumbnail($product),
                'variant_id' => $variant?->id,
                'variant_name' => $variant ? $this->getVariantName($variant) : null,
                'quantity' => $qty,
                'unit_price' => $price,
                'subtotal' => $price * $qty,
            ];
        }

        $this->items = collect($items);
    }

    protected function getProductThumbnail(?object $product): ?string
    {
        if (! $product) {
            return null;
        }

        $media = DB::table('media')
            ->where('model_type', 'LIKE', '%Product%')
            ->where('model_id', $product->id)
            ->orderBy('order_column')
            ->first();

        return $media ? asset('storage/'.$media->id.'/'.$media->file_name) : null;
    }

    protected function getVariantName(object $variant): string
    {
        $options = DB::table('sh_product_variant_values')
            ->join('sh_attribute_values', 'sh_product_variant_values.attribute_value_id', '=', 'sh_attribute_values.id')
            ->where('sh_product_variant_values.product_variant_id', $variant->id)
            ->pluck('sh_attribute_values.value')
            ->toArray();

        return implode(' / ', $options);
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getSubtotal(): float
    {
        return round($this->items->sum('subtotal'), 2);
    }

    public function getDiscount(): float
    {
        if (! $this->couponCode) {
            return 0;
        }

        $subtotal = $this->getSubtotal();

        return round($subtotal * 0.1, 2);
    }

    public function getShippingEstimate(): float
    {
        $subtotal = $this->getSubtotal();

        return $subtotal >= 50 ? 0 : 5.99;
    }

    public function getTotal(): float
    {
        return round($this->getSubtotal() - $this->getDiscount() + $this->getShippingEstimate(), 2);
    }

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function addItem(int $productId, int $qty, ?int $variantId = null): array
    {
        if (! $this->cartId) {
            $this->resolveCart();
        }

        $purchasableType = $variantId
            ? 'Shopper\Core\Models\ProductVariant'
            : 'Shopper\Core\Models\Product';

        $stockQty = $this->getStockQuantity($purchasableType, $variantId ?? $productId);

        if ($qty > $stockQty) {
            return [
                'success' => false,
                'error' => "Only {$stockQty} items available",
                'code' => 422,
            ];
        }

        $existingLine = DB::table('sh_cart_lines')
            ->where('cart_id', $this->cartId)
            ->where('purchasable_type', $purchasableType)
            ->where('purchasable_id', $variantId ?? $productId)
            ->first();

        if ($existingLine) {
            $newQty = $existingLine->quantity + $qty;

            if ($newQty > $stockQty) {
                return [
                    'success' => false,
                    'error' => "Only {$stockQty} items available",
                    'code' => 422,
                ];
            }

            $price = $this->getProductPrice($purchasableType, $variantId ?? $productId);

            DB::table('sh_cart_lines')
                ->where('id', $existingLine->id)
                ->update([
                    'quantity' => $newQty,
                    'unit_price_amount' => $price * 100,
                    'updated_at' => now(),
                ]);
        } else {
            $price = $this->getProductPrice($purchasableType, $variantId ?? $productId);

            DB::table('sh_cart_lines')->insert([
                'cart_id' => $this->cartId,
                'purchasable_type' => $purchasableType,
                'purchasable_id' => $variantId ?? $productId,
                'quantity' => $qty,
                'unit_price_amount' => $price * 100,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->loadItems();

        return ['success' => true];
    }

    public function updateItem(int $itemId, int $qty): array
    {
        $line = DB::table('sh_cart_lines')
            ->where('id', $itemId)
            ->where('cart_id', $this->cartId)
            ->first();

        if (! $line) {
            return [
                'success' => false,
                'error' => 'Item not found in cart',
                'code' => 404,
            ];
        }

        $stockQty = $this->getStockQuantity($line->purchasable_type, $line->purchasable_id);

        if ($qty < 1 || $qty > $stockQty) {
            return [
                'success' => false,
                'error' => "Quantity must be between 1 and {$stockQty}",
                'code' => 422,
            ];
        }

        DB::table('sh_cart_lines')
            ->where('id', $itemId)
            ->update([
                'quantity' => $qty,
                'updated_at' => now(),
            ]);

        $this->loadItems();

        return ['success' => true];
    }

    public function removeItem(int $itemId): bool
    {
        $deleted = DB::table('sh_cart_lines')
            ->where('id', $itemId)
            ->where('cart_id', $this->cartId)
            ->delete();

        if ($deleted) {
            $this->loadItems();
        }

        return $deleted > 0;
    }

    public function applyCoupon(string $code): array
    {
        $coupon = DB::table('sh_discounts')
            ->where('code', strtoupper($code))
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->where('is_active', true)
            ->first();

        if (! $coupon) {
            return [
                'success' => false,
                'error' => 'Invalid or expired coupon code',
                'code' => 422,
            ];
        }

        DB::table('sh_carts')
            ->where('id', $this->cartId)
            ->update([
                'coupon_code' => strtoupper($code),
                'updated_at' => now(),
            ]);

        $this->couponCode = strtoupper($code);

        return ['success' => true];
    }

    public function removeCoupon(): void
    {
        DB::table('sh_carts')
            ->where('id', $this->cartId)
            ->update([
                'coupon_code' => null,
                'updated_at' => now(),
            ]);

        $this->couponCode = null;
    }

    public function mergeGuestCart(string $guestId): void
    {
        $guestCart = DB::table('sh_carts')
            ->whereJsonContains('metadata', ['guest_id' => $guestId])
            ->whereNull('completed_at')
            ->first();

        if (! $guestCart) {
            return;
        }

        $guestLines = DB::table('sh_cart_lines')
            ->where('cart_id', $guestCart->id)
            ->get();

        foreach ($guestLines as $line) {
            $this->addItem($line->purchasable_id, $line->quantity);
        }

        DB::table('sh_cart_lines')
            ->where('cart_id', $guestCart->id)
            ->delete();

        DB::table('sh_carts')
            ->where('id', $guestCart->id)
            ->delete();
    }

    public function getCartId(): ?int
    {
        return $this->cartId;
    }

    protected function getStockQuantity(string $type, int $id): int
    {
        if ($type === 'Shopper\Core\Models\ProductVariant') {
            $variant = DB::table('sh_product_variants')->where('id', $id)->first();

            return (int) ($variant?->stock ?? 0);
        }

        $product = DB::table('sh_products')->where('id', $id)->first();

        return (int) ($product?->stock ?? 0);
    }

    protected function getProductPrice(string $type, int $id): float
    {
        if ($type === 'Shopper\Core\Models\ProductVariant') {
            $variant = DB::table('sh_product_variants')->where('id', $id)->first();

            return (float) (($variant?->price ?? 0) / 100);
        }

        $currency = 'USD';
        $price = DB::table('sh_prices')
            ->join('sh_currencies', 'sh_prices.currency_id', '=', 'sh_currencies.id')
            ->where('sh_prices.priceable_type', 'Shopper\Core\Models\Product')
            ->where('sh_prices.priceable_id', $id)
            ->where('sh_currencies.code', $currency)
            ->first();

        return $price ? (float) ($price->amount / 100) : 0.0;
    }

    public function toArray(): array
    {
        return [
            'items' => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'thumbnail' => $item->thumbnail,
                'variant_id' => $item->variant_id,
                'variant_name' => $item->variant_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->subtotal,
            ])->toArray(),
            'subtotal' => $this->getSubtotal(),
            'discount' => $this->getDiscount(),
            'shipping_estimate' => $this->getShippingEstimate(),
            'total' => $this->getTotal(),
            'coupon_code' => $this->getCouponCode(),
        ];
    }

    public function getCart(): array
    {
        return $this->toArray();
    }
}
