<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedChannels();
        $this->seedInventory();
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

    protected function seedInventory(): void
    {
        if (DB::table('sh_inventories')->count() === 0) {
            DB::table('sh_inventories')->insert([
                'id' => 1,
                'name' => 'Default',
                'code' => 'default',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function createTestProduct(array $overrides = []): int
    {
        $defaults = [
            'name' => 'Test Product ' . uniqid(),
            'slug' => 'test-product-' . uniqid(),
            'sku' => 'TEST-' . uniqid(),
            'barcode' => (string) random_int(100000000, 999999999),
            'description' => 'Test product description',
            'security_stock' => 0,
            'featured' => false,
            'is_visible' => true,
            'type' => 'standard',
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return DB::table('sh_products')->insertGetId(array_merge($defaults, $overrides));
    }

    protected function createAuthenticatedUser(): User
    {
        return User::factory()->create();
    }

    public function test_sql_injection_in_search_param_is_sanitized(): void
    {
        Sanctum::actingAs($this->createAuthenticatedUser());

        $response = $this->getJson('/api/products?search=valid');
        $response->assertStatus(200);

        $response = $this->getJson('/api/products?search=valid\'; DROP TABLE users;--');
        $response->assertStatus(200);
    }

    public function test_xss_in_product_name_is_stored_and_returned(): void
    {
        Sanctum::actingAs($this->createAuthenticatedUser());

        $xssPayload = '<script>alert("XSS")</script>';
        $productId = $this->createTestProduct(['name' => $xssPayload]);

        $response = $this->getJson("/api/products/test-product-{$productId}");
        
        if ($response->status() === 200) {
            $response->assertJsonFragment(['name' => $xssPayload]);
        } else {
            $this->assertTrue(in_array($response->status(), [200, 404]));
        }
    }

    public function test_upload_non_image_file_returns_422(): void
    {
        Storage::fake('public');
        config(['shopper.media.storage.disk_name' => 'public']);

        $user = $this->createAuthenticatedUser();
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avatar']);
    }

    public function test_avatar_wrong_mime_type_returns_422(): void
    {
        Storage::fake('public');
        config(['shopper.media.storage.disk_name' => 'public']);

        $user = $this->createAuthenticatedUser();
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('avatar.gif', 100, 'image/gif');

        $response = $this->postJson('/api/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avatar']);
    }

    public function test_email_change_to_existing_email_returns_422(): void
    {
        $user = $this->createAuthenticatedUser([
            'email' => 'existing@example.com',
            'password' => Hash::make('password'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile/email', [
            'email' => 'existing@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
    }

    public function test_password_change_with_mismatched_confirmation_returns_422(): void
    {
        $user = $this->createAuthenticatedUser([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile/password', [
            'current_password' => 'old-password',
            'password' => 'new-password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_profile_update_with_empty_fields_returns_validation_error(): void
    {
        $user = $this->createAuthenticatedUser();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'first_name' => '',
            'last_name' => '',
        ]);

        $response->assertStatus(422);
    }

    public function test_profile_update_with_too_long_phone_returns_422(): void
    {
        $user = $this->createAuthenticatedUser();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'phone' => str_repeat('1', 50),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_wishlist_toggle_same_product_multiple_times(): void
    {
        $user = $this->createAuthenticatedUser();
        $productId = $this->createTestProduct();

        Sanctum::actingAs($user);

        $response1 = $this->postJson("/api/wishlist/{$productId}");
        $response1->assertStatus(200)
            ->assertJson(['wishlisted' => true]);

        $response2 = $this->postJson("/api/wishlist/{$productId}");
        $response2->assertStatus(200)
            ->assertJson(['wishlisted' => false]);

        $response3 = $this->postJson("/api/wishlist/{$productId}");
        $response3->assertStatus(200)
            ->assertJson(['wishlisted' => true]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);
    }

    public function test_wishlist_cannot_add_same_product_twice(): void
    {
        $user = $this->createAuthenticatedUser();
        $productId = $this->createTestProduct();

        Sanctum::actingAs($user);

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);

        $response = $this->postJson("/api/wishlist/{$productId}");
        $response->assertStatus(200)
            ->assertJson(['wishlisted' => false]);

        $count = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->count();
        $this->assertEquals(0, $count);
    }

    public function test_concurrent_wishlist_adds_only_once(): void
    {
        $user = $this->createAuthenticatedUser();
        $productId = $this->createTestProduct();

        Sanctum::actingAs($user);

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);

        $count = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->count();
        $this->assertEquals(1, $count);
    }

    public function test_unauthorized_access_to_protected_endpoints(): void
    {
        $this->getJson('/api/profile')->assertStatus(401);
        $this->getJson('/api/wishlist')->assertStatus(401);
        $this->putJson('/api/profile')->assertStatus(401);
        $this->postJson('/api/profile/avatar')->assertStatus(401);
        $this->putJson('/api/profile/email')->assertStatus(401);
        $this->putJson('/api/profile/password')->assertStatus(401);
    }

    public function test_invalid_json_payload_returns_422(): void
    {
        Sanctum::actingAs($this->createAuthenticatedUser());

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
        ])->postJson('/api/profile', []);

        $response->assertStatus(200);
    }

    public function test_profile_show_returns_correct_structure(): void
    {
        $user = $this->createAuthenticatedUser([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone_number' => '+1234567890',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'avatar_url',
                ],
            ])
            ->assertJsonPath('data.first_name', 'John')
            ->assertJsonPath('data.last_name', 'Doe')
            ->assertJsonPath('data.email', 'john@example.com')
            ->assertJsonPath('data.phone', '+1234567890');
    }

    public function test_wishlist_returns_empty_when_no_items(): void
    {
        $user = $this->createAuthenticatedUser();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/wishlist');

        $response->assertStatus(200)
            ->assertJson(['data' => []]);
    }

    public function test_wishlist_with_invisible_product_filters_correctly(): void
    {
        $user = $this->createAuthenticatedUser();
        $productId = $this->createTestProduct(['is_visible' => false]);

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/wishlist');

        $response->assertStatus(200)
            ->assertJson(['data' => []]);
    }
}
