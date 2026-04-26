<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Shopper\Core\Enum\OrderStatus;
use Shopper\Core\Enum\PaymentStatus;
use Shopper\Core\Enum\ShippingStatus;
use Shopper\Core\Models\Order;
use Shopper\Core\Models\OrderItem;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedCountries();
        $this->seedChannels();
    }

    protected function seedCountries(): void
    {
        if (DB::table('sh_countries')->count() === 0) {
            DB::table('sh_countries')->insert([
                [
                    'id' => 1,
                    'name' => 'United States',
                    'name_official' => 'United States of America',
                    'cca2' => 'US',
                    'cca3' => 'USA',
                    'region' => 'Americas',
                    'subregion' => 'Northern America',
                    'phone_calling_code' => json_encode('+1'),
                    'currencies' => json_encode(['USD']),
                    'flag' => '🇺🇸',
                    'latitude' => 37.090240,
                    'longitude' => -95.712891,
                ],
            ]);
        }
    }

    protected function seedChannels(): void
    {
        if (DB::table('sh_channels')->count() === 0) {
            DB::table('sh_channels')->insert([
                'id' => 1,
                'name' => 'Default Channel',
                'slug' => 'default',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function createTestProduct(int $stock = 10): int
    {
        $productId = DB::table('sh_products')->insertGetId([
            'name' => 'Test Product '.uniqid(),
            'slug' => 'test-product-'.uniqid(),
            'sku' => 'TEST-'.uniqid(),
            'barcode' => (string) random_int(100000000, 999999999),
            'description' => 'Test product description',
            'security_stock' => 0,
            'featured' => false,
            'is_visible' => true,
            'type' => 'standard',
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $inventoryId = DB::table('sh_inventories')->insertGetId([
            'name' => 'Main Warehouse',
            'code' => 'MAIN-'.uniqid(),
            'email' => 'warehouse@test.com',
            'street_address' => '123 Test St',
            'postal_code' => '12345',
            'city' => 'Test City',
            'country_id' => 1,
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sh_inventory_histories')->insert([
            'inventory_id' => $inventoryId,
            'stockable_type' => 'Shopper\Core\Models\Product',
            'stockable_id' => $productId,
            'quantity' => $stock,
            'event' => 'test_setup',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $productId;
    }

    protected function createOrder(User $user, array $attributes = []): Order
    {
        $order = Order::create(array_merge([
            'number' => 'ORD-'.uniqid(),
            'customer_id' => $user->id,
            'currency_code' => 'USD',
            'status' => OrderStatus::New,
            'payment_status' => PaymentStatus::Pending,
            'shipping_status' => ShippingStatus::Unfulfilled,
            'notes' => 'Test order',
        ], $attributes));

        return $order;
    }

    protected function createOrderItem(Order $order, int $productId, int $quantity = 1, int $price = 1999): OrderItem
    {
        return OrderItem::create([
            'order_id' => $order->id,
            'product_type' => 'Shopper\Core\Models\Product',
            'product_id' => $productId,
            'name' => 'Test Product',
            'sku' => 'TEST-SKU',
            'quantity' => $quantity,
            'unit_price_amount' => $price,
            'discount_amount' => 0,
        ]);
    }

    public function test_list_returns_only_authenticated_users_orders(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $order1 = $this->createOrder($user1);
        $this->createOrder($user2);

        Sanctum::actingAs($user1);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.order_number', $order1->number);
    }

    public function test_list_filters_by_status(): void
    {
        $user = User::factory()->create();

        $this->createOrder($user, ['status' => OrderStatus::New]);
        $this->createOrder($user, ['status' => OrderStatus::Processing]);
        $this->createOrder($user, ['status' => OrderStatus::Completed]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/orders?status=pending');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $response = $this->getJson('/api/orders?status=processing');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_list_respects_per_page_limit(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->createOrder($user);
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/orders?per_page=2');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 5);
    }

    public function test_list_requires_authentication(): void
    {
        $response = $this->getJson('/api/orders');

        $response->assertStatus(401);
    }

    public function test_order_detail_returns_full_order(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user);
        $productId = $this->createTestProduct();

        $this->createOrderItem($order, $productId);

        $addressId = DB::table('sh_order_addresses')->insertGetId([
            'customer_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street_address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country_name' => 'United States',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $order->update(['shipping_address_id' => $addressId]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/orders/{$order->number}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'order_number',
                    'status',
                    'payment_status',
                    'shipping_status',
                    'created_at',
                    'updated_at',
                    'total',
                    'currency',
                    'items_count',
                    'items' => [
                        '*' => [
                            'id',
                            'name',
                            'sku',
                            'quantity',
                            'unit_price',
                            'total',
                        ],
                    ],
                    'shipping_address' => [
                        'first_name',
                        'last_name',
                        'address1',
                        'city',
                    ],
                    'timeline',
                ],
            ]);
    }

    public function test_order_detail_includes_timeline_events(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/orders/{$order->number}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'timeline' => [
                        '*' => [
                            'status',
                            'timestamp',
                            'note',
                        ],
                    ],
                ],
            ]);

        $timeline = $response->json('data.timeline');
        $this->assertNotEmpty($timeline);
        $this->assertEquals('created', $timeline[0]['status']);
    }

    public function test_another_user_cannot_view_my_order(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $order = $this->createOrder($user1);

        Sanctum::actingAs($user2);

        $response = $this->getJson("/api/orders/{$order->number}");

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized',
            ]);
    }

    public function test_cancel_pending_order_succeeds_and_restock_items(): void
    {
        $user = User::factory()->create();
        $productId = $this->createTestProduct(stock: 10);

        $order = $this->createOrder($user, [
            'status' => OrderStatus::New,
            'shipping_status' => ShippingStatus::Unfulfilled,
        ]);

        $this->createOrderItem($order, $productId, quantity: 3);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/orders/{$order->number}/cancel");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Order cancelled successfully',
            ])
            ->assertJsonPath('data.status', 'cancelled');

        $order->refresh();
        $this->assertEquals(OrderStatus::Cancelled, $order->status);

        $stockAfter = DB::table('sh_inventory_histories')
            ->where('stockable_id', $productId)
            ->where('stockable_type', 'Shopper\Core\Models\Product')
            ->sum('quantity');

        $this->assertEquals(13, $stockAfter);
    }

    public function test_cancel_processing_order_succeeds(): void
    {
        $user = User::factory()->create();
        $productId = $this->createTestProduct(stock: 5);

        $order = $this->createOrder($user, [
            'status' => OrderStatus::Processing,
            'shipping_status' => ShippingStatus::Unfulfilled,
        ]);

        $this->createOrderItem($order, $productId, quantity: 2);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/orders/{$order->number}/cancel");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled');
    }

    public function test_cancel_shipped_order_returns_422(): void
    {
        $user = User::factory()->create();

        $order = $this->createOrder($user, [
            'status' => OrderStatus::Processing,
            'shipping_status' => ShippingStatus::Shipped,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/orders/{$order->number}/cancel");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Order cannot be cancelled as it has been shipped or delivered',
            ]);
    }

    public function test_cancel_completed_order_returns_422(): void
    {
        $user = User::factory()->create();

        $order = $this->createOrder($user, [
            'status' => OrderStatus::Completed,
            'shipping_status' => ShippingStatus::Delivered,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/orders/{$order->number}/cancel");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Order cannot be cancelled in its current status',
            ]);
    }

    public function test_cancel_order_requires_authentication(): void
    {
        $user = User::factory()->create();
        $order = $this->createOrder($user);

        $response = $this->postJson("/api/orders/{$order->number}/cancel");

        $response->assertStatus(401);
    }

    public function test_cancel_order_not_found(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/orders/NONEXISTENT-123/cancel');

        $response->assertStatus(404);
    }

    public function test_cancel_order_unauthorized_for_other_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $order = $this->createOrder($user1);

        Sanctum::actingAs($user2);

        $response = $this->postJson("/api/orders/{$order->number}/cancel");

        $response->assertStatus(403);
    }
}
