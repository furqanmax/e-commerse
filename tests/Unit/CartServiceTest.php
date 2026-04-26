<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\CartService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class CartServiceTest extends TestCase
{
    public function test_cart_service_can_be_instantiated(): void
    {
        $cartService = new CartService;

        $this->assertInstanceOf(CartService::class, $cartService);
    }

    public function test_cart_service_to_array_returns_correct_structure(): void
    {
        $cartService = new CartService;

        $array = $cartService->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('items', $array);
        $this->assertArrayHasKey('subtotal', $array);
        $this->assertArrayHasKey('discount', $array);
        $this->assertArrayHasKey('shipping_estimate', $array);
        $this->assertArrayHasKey('total', $array);
        $this->assertArrayHasKey('coupon_code', $array);
    }

    public function test_cart_service_returns_empty_items_by_default(): void
    {
        $cartService = new CartService;

        $array = $cartService->toArray();

        $this->assertIsArray($array['items']);
        $this->assertEmpty($array['items']);
    }

    public function test_cart_service_handles_null_user(): void
    {
        $cartService = new CartService(null);

        $this->assertInstanceOf(CartService::class, $cartService);
    }

    public function test_cart_service_set_guest_id_returns_self(): void
    {
        $cartService = new CartService;

        $result = $cartService->setGuestId('test-guest');

        $this->assertInstanceOf(CartService::class, $result);
    }

    public function test_cart_service_get_subtotal_returns_float(): void
    {
        $cartService = new CartService;

        $subtotal = $cartService->getSubtotal();

        $this->assertIsFloat($subtotal);
        $this->assertEquals(0.0, $subtotal);
    }

    public function test_cart_service_get_discount_returns_float(): void
    {
        $cartService = new CartService;

        $discount = $cartService->getDiscount();

        $this->assertIsFloat($discount);
        $this->assertEquals(0.0, $discount);
    }

    public function test_cart_service_get_total_returns_float(): void
    {
        $cartService = new CartService;

        $total = $cartService->getTotal();

        $this->assertIsFloat($total);
        $this->assertEquals(5.99, $total);
    }

    public function test_cart_service_get_coupon_code_returns_null_by_default(): void
    {
        $cartService = new CartService;

        $this->assertNull($cartService->getCouponCode());
    }

    public function test_cart_service_get_shipping_estimate_returns_float(): void
    {
        $cartService = new CartService;

        $shipping = $cartService->getShippingEstimate();

        $this->assertIsFloat($shipping);
        $this->assertEquals(5.99, $shipping);
    }

    public function test_cart_service_get_items_returns_collection(): void
    {
        $cartService = new CartService;

        $items = $cartService->getItems();

        $this->assertInstanceOf(Collection::class, $items);
        $this->assertTrue($items->isEmpty());
    }

    public function test_cart_service_get_cart_id_returns_null_by_default(): void
    {
        $cartService = new CartService;

        $this->assertNull($cartService->getCartId());
    }
}
