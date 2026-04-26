<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedChannels();
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

    protected function createTestProduct(): int
    {
        return DB::table('sh_products')->insertGetId([
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
        ]);
    }

    public function test_profile_show_returns_user_data(): void
    {
        $user = User::factory()->create([
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
            ->assertJsonPath('data.email', 'john@example.com');
    }

    public function test_profile_update_persists_changes(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone' => '+1987654321',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.first_name', 'Jane')
            ->assertJsonPath('data.last_name', 'Smith')
            ->assertJsonPath('data.phone', '+1987654321');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);
    }

    public function test_profile_update_requires_authentication(): void
    {
        $response = $this->putJson('/api/profile', [
            'first_name' => 'Jane',
        ]);

        $response->assertStatus(401);
    }

    public function test_avatar_upload_stores_file_and_returns_url(): void
    {
        Storage::fake('public');
        config(['shopper.media.storage.disk_name' => 'public']);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->postJson('/api/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['avatar_url'],
                'message',
            ]);

        $this->assertNotEmpty($response->json('data.avatar_url'));

        $user->refresh();
        $this->assertEquals('storage', $user->avatar_type);
        $this->assertNotEmpty($user->avatar_location);
    }

    public function test_avatar_too_large_returns_422(): void
    {
        Storage::fake('public');
        config(['shopper.media.storage.disk_name' => 'public']);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('avatar.jpg')->size(3000);

        $response = $this->postJson('/api/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avatar']);
    }

    public function test_avatar_invalid_type_returns_422(): void
    {
        Storage::fake('public');
        config(['shopper.media.storage.disk_name' => 'public']);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avatar']);
    }

    public function test_email_change_requires_correct_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile/email', [
            'email' => 'new@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'The provided password is incorrect.']);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'email' => 'new@example.com',
        ]);
    }

    public function test_email_change_with_correct_password(): void
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile/email', [
            'email' => 'new@example.com',
            'password' => 'correct-password',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.email', 'new@example.com');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'new@example.com',
        ]);
    }

    public function test_password_change_with_wrong_current_password_returns_422(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'The current password is incorrect.']);
    }

    public function test_password_change_with_correct_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/profile/password', [
            'current_password' => 'old-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Password updated successfully.']);

        $user->refresh();
        $this->assertTrue(Hash::check('new-password123', $user->password));
    }

    public function test_wishlist_returns_user_items(): void
    {
        $user = User::factory()->create();

        $productId = $this->createTestProduct();
        DB::table('sh_products')->where('id', $productId)->update(['name' => 'Test Product']);

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/wishlist');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'price',
                        'thumbnail',
                        'is_visible',
                    ],
                ],
            ])
            ->assertJsonPath('data.0.name', 'Test Product');
    }

    public function test_wishlist_toggle_adds_product(): void
    {
        $user = User::factory()->create();
        $productId = $this->createTestProduct();

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/wishlist/{$productId}");

        $response->assertStatus(200)
            ->assertJson(['wishlisted' => true]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);
    }

    public function test_wishlist_toggle_removes_product(): void
    {
        $user = User::factory()->create();
        $productId = $this->createTestProduct();

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/wishlist/{$productId}");

        $response->assertStatus(200)
            ->assertJson(['wishlisted' => false]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $productId,
        ]);
    }

    public function test_wishlist_toggle_nonexistent_product_returns_404(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/wishlist/99999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Product not found.']);
    }

    public function test_wishlist_requires_authentication(): void
    {
        $response = $this->getJson('/api/wishlist');
        $response->assertStatus(401);

        $productId = $this->createTestProduct();
        $response = $this->postJson("/api/wishlist/{$productId}");
        $response->assertStatus(401);
    }
}
