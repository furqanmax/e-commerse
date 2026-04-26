<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Shopper\Core\Models\Address;
use Tests\TestCase;

class CheckoutTest extends TestCase
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
                [
                    'id' => 2,
                    'name' => 'Canada',
                    'name_official' => 'Canada',
                    'cca2' => 'CA',
                    'cca3' => 'CAN',
                    'region' => 'Americas',
                    'subregion' => 'Northern America',
                    'phone_calling_code' => json_encode('+1'),
                    'currencies' => json_encode(['CAD']),
                    'flag' => '🇨🇦',
                    'latitude' => 56.130367,
                    'longitude' => -106.346771,
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

    public function test_create_address_validates_required_fields(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/addresses', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'address1', 'city', 'state', 'postcode', 'country_code']);
    }

    public function test_create_address_validates_country_code_format(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/addresses', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address1' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postcode' => '10001',
            'country_code' => 'INVALID',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['country_code']);
    }

    public function test_can_create_address(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/addresses', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '+1234567890',
            'address1' => '123 Main St',
            'address2' => 'Apt 4B',
            'city' => 'New York',
            'state' => 'NY',
            'postcode' => '10001',
            'country_code' => 'US',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                    'phone',
                    'address1',
                    'city',
                    'state',
                    'postcode',
                    'country_code',
                    'is_default',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('sh_user_addresses', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'user_id' => $user->id,
        ]);
    }

    public function test_first_address_is_automatically_default(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/addresses', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address1' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postcode' => '10001',
            'country_code' => 'US',
        ]);

        $response->assertStatus(201);

        $address = Address::where('user_id', $user->id)->first();
        $this->assertTrue((bool) $address->shipping_default);
    }

    public function test_cannot_modify_another_users_address(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $address = Address::create([
            'user_id' => $user1->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street_address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country_id' => 1,
            'shipping_default' => true,
            'billing_default' => false,
            'type' => 'shipping',
        ]);

        Sanctum::actingAs($user2);

        $response = $this->putJson("/api/addresses/{$address->id}", [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'address1' => '456 Oak Ave',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'postcode' => '90001',
            'country_code' => 'US',
        ]);

        $response->assertStatus(404);
    }

    public function test_can_delete_own_address(): void
    {
        $user = User::factory()->create();

        $address = Address::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street_address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country_id' => 1,
            'shipping_default' => true,
            'billing_default' => false,
            'type' => 'shipping',
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/addresses/{$address->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('sh_user_addresses', ['id' => $address->id]);
    }

    public function test_address_limit_max_five_addresses(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        for ($i = 1; $i <= 5; $i++) {
            $response = $this->postJson('/api/addresses', [
                'first_name' => "John{$i}",
                'last_name' => 'Doe',
                'address1' => "{$i}23 Main St",
                'city' => 'New York',
                'state' => 'NY',
                'postcode' => '10001',
                'country_code' => 'US',
            ]);

            $response->assertStatus(201);
        }

        $response = $this->postJson('/api/addresses', [
            'first_name' => 'John6',
            'last_name' => 'Doe',
            'address1' => '623 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postcode' => '10001',
            'country_code' => 'US',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['address']);
    }

    public function test_can_set_default_address(): void
    {
        $user = User::factory()->create();

        $address1 = Address::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street_address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country_id' => 1,
            'shipping_default' => true,
            'billing_default' => false,
            'type' => 'shipping',
        ]);

        $address2 = Address::create([
            'user_id' => $user->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'street_address' => '456 Oak Ave',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'postal_code' => '90001',
            'country_id' => 1,
            'shipping_default' => false,
            'billing_default' => false,
            'type' => 'shipping',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/addresses/{$address2->id}/default");

        $response->assertStatus(200);

        $address1->refresh();
        $address2->refresh();

        $this->assertFalse((bool) $address1->shipping_default);
        $this->assertTrue((bool) $address2->shipping_default);
    }

    public function test_checkout_session_with_empty_cart_returns_422(): void
    {
        $user = User::factory()->create();

        $address = Address::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street_address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country_id' => 1,
            'shipping_default' => true,
            'billing_default' => false,
            'type' => 'shipping',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/checkout/session', [
            'shipping_address_id' => $address->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cart']);
    }

    public function test_checkout_session_with_out_of_stock_item_returns_422(): void
    {
        $user = User::factory()->create();

        $productId = $this->createTestProduct(stock: 0);

        $address = Address::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street_address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country_id' => 1,
            'shipping_default' => true,
            'billing_default' => false,
            'type' => 'shipping',
        ]);

        $cartId = DB::table('sh_carts')->insertGetId([
            'customer_id' => $user->id,
            'channel_id' => 1,
            'currency_code' => 'USD',
            'metadata' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sh_cart_lines')->insert([
            'cart_id' => $cartId,
            'purchasable_type' => 'Shopper\Core\Models\Product',
            'purchasable_id' => $productId,
            'quantity' => 1,
            'unit_price_amount' => 1999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/checkout/session', [
            'shipping_address_id' => $address->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_checkout_session_returns_correct_structure(): void
    {
        $user = User::factory()->create();

        $productId = $this->createTestProduct(stock: 10);

        $address = Address::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street_address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country_id' => 1,
            'shipping_default' => true,
            'billing_default' => false,
            'type' => 'shipping',
        ]);

        $cartId = DB::table('sh_carts')->insertGetId([
            'customer_id' => $user->id,
            'channel_id' => 1,
            'currency_code' => 'USD',
            'metadata' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sh_cart_lines')->insert([
            'cart_id' => $cartId,
            'purchasable_type' => 'Shopper\Core\Models\Product',
            'purchasable_id' => $productId,
            'quantity' => 1,
            'unit_price_amount' => 1999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/checkout/session', [
            'shipping_address_id' => $address->id,
            'shipping_method_id' => 'standard',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'session_id',
                'shipping_address',
                'items_summary',
                'subtotal',
                'shipping',
                'discount',
                'tax',
                'total',
                'available_shipping_methods',
                'payment_methods',
            ]);
    }

    public function test_checkout_confirm_creates_order(): void
    {
        $user = User::factory()->create();

        $productId = $this->createTestProduct(stock: 10);

        $address = Address::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street_address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country_id' => 1,
            'shipping_default' => true,
            'billing_default' => false,
            'type' => 'shipping',
        ]);

        $cartId = DB::table('sh_carts')->insertGetId([
            'customer_id' => $user->id,
            'channel_id' => 1,
            'currency_code' => 'USD',
            'metadata' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sh_cart_lines')->insert([
            'cart_id' => $cartId,
            'purchasable_type' => 'Shopper\Core\Models\Product',
            'purchasable_id' => $productId,
            'quantity' => 2,
            'unit_price_amount' => 1999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $sessionResponse = $this->postJson('/api/checkout/session', [
            'shipping_address_id' => $address->id,
            'shipping_method_id' => 'standard',
        ]);

        $sessionId = $sessionResponse->json('session_id');

        $response = $this->postJson('/api/checkout/confirm', [
            'session_id' => $sessionId,
            'payment_method' => 'cod',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'order_number',
                'status',
                'total',
                'estimated_delivery',
                'message',
            ]);

        $this->assertDatabaseHas('sh_orders', [
            'customer_id' => $user->id,
            'shipping_address_id' => $address->id,
        ]);

        $remainingStock = DB::table('sh_inventory_histories')
            ->where('stockable_id', $productId)
            ->where('stockable_type', 'Shopper\Core\Models\Product')
            ->sum('quantity');
        $this->assertEquals(8, $remainingStock);
    }

    public function test_checkout_confirm_reduces_stock(): void
    {
        $user = User::factory()->create();

        $productId = $this->createTestProduct(stock: 5);

        $address = Address::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street_address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country_id' => 1,
            'shipping_default' => true,
            'billing_default' => false,
            'type' => 'shipping',
        ]);

        $cartId = DB::table('sh_carts')->insertGetId([
            'customer_id' => $user->id,
            'channel_id' => 1,
            'currency_code' => 'USD',
            'metadata' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sh_cart_lines')->insert([
            'cart_id' => $cartId,
            'purchasable_type' => 'Shopper\Core\Models\Product',
            'purchasable_id' => $productId,
            'quantity' => 3,
            'unit_price_amount' => 1000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $sessionResponse = $this->postJson('/api/checkout/session', [
            'shipping_address_id' => $address->id,
        ]);

        $sessionId = $sessionResponse->json('session_id');

        $this->postJson('/api/checkout/confirm', [
            'session_id' => $sessionId,
            'payment_method' => 'cod',
        ]);

        $remainingStock = DB::table('sh_inventory_histories')
            ->where('stockable_id', $productId)
            ->where('stockable_type', 'Shopper\Core\Models\Product')
            ->sum('quantity');
        $this->assertEquals(2, $remainingStock);
    }

    public function test_checkout_confirm_payment_failure_does_not_create_order(): void
    {
        $user = User::factory()->create();

        $productId = DB::table('sh_products')->insertGetId([
            'name' => 'Test Product',
            'slug' => 'test-product-fail',
            'sku' => 'TEST-FAIL',
            'barcode' => '123456789',
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
            'name' => 'Default',
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sh_inventory_histories')->insert([
            'inventory_id' => $inventoryId,
            'stockable_type' => 'Shopper\Core\Models\Product',
            'stockable_id' => $productId,
            'quantity' => 10,
            'event' => 'test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $address = Address::create([
            'user_id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'street_address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country_id' => 1,
            'shipping_default' => true,
            'billing_default' => false,
            'type' => 'shipping',
        ]);

        $cartId = DB::table('sh_carts')->insertGetId([
            'customer_id' => $user->id,
            'channel_id' => 1,
            'currency_code' => 'USD',
            'metadata' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('sh_cart_lines')->insert([
            'cart_id' => $cartId,
            'purchasable_type' => 'Shopper\Core\Models\Product',
            'purchasable_id' => $productId,
            'quantity' => 1,
            'unit_price_amount' => 1999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $sessionResponse = $this->postJson('/api/checkout/session', [
            'shipping_address_id' => $address->id,
        ]);

        $sessionId = $sessionResponse->json('session_id');

        DB::table('sh_inventory_histories')
            ->where('stockable_id', $productId)
            ->where('stockable_type', 'Shopper\Core\Models\Product')
            ->update(['quantity' => 0]);

        $response = $this->postJson('/api/checkout/confirm', [
            'session_id' => $sessionId,
            'payment_method' => 'stripe',
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseMissing('sh_orders', [
            'customer_id' => $user->id,
        ]);
    }

    public function test_checkout_session_requires_shipping_address(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/checkout/session', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['shipping_address_id']);
    }
}
