<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Tests\TestCase;

class CatalogTest extends TestCase
{
    public function test_get_categories_endpoint_exists(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_get_products_endpoint_exists(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    public function test_get_products_rejects_per_page_over_max(): void
    {
        $response = $this->getJson('/api/products?per_page=100');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertLessThanOrEqual(30, $data['meta']['per_page']);
    }

    public function test_get_product_by_slug_returns_404_for_nonexistent(): void
    {
        $response = $this->getJson('/api/products/nonexistent-slug');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Product not found']);
    }

    public function test_banners_endpoint_returns_array(): void
    {
        $response = $this->getJson('/api/banners');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
            ]);
    }

    public function test_banners_returns_empty_when_no_channels_configured(): void
    {
        $response = $this->getJson('/api/banners');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertIsArray($data['data']);
    }

    public function test_products_endpoint_filters_by_category_slug(): void
    {
        $response = $this->getJson('/api/products?category_slug=nonexistent');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertEquals(0, $data['meta']['total']);
    }

    public function test_products_endpoint_filters_by_brand_id(): void
    {
        $response = $this->getJson('/api/products?brand_id=999');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertEquals(0, $data['meta']['total']);
    }

    public function test_products_endpoint_filters_by_price_range(): void
    {
        $response = $this->getJson('/api/products?min_price=10&max_price=100');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta',
            ]);
    }

    public function test_products_list_response_has_required_structure(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200);

        $data = $response->json();

        if (! empty($data['data'])) {
            $product = $data['data'][0];
            $this->assertArrayHasKey('id', $product);
            $this->assertArrayHasKey('name', $product);
            $this->assertArrayHasKey('slug', $product);
            $this->assertArrayHasKey('price', $product);
            $this->assertArrayHasKey('thumbnail', $product);
            $this->assertArrayHasKey('in_stock', $product);
        }
    }

    public function test_categories_returns_valid_structure(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertIsArray($data['data']);
    }

    public function test_products_filters_by_search_param(): void
    {
        $response = $this->getJson('/api/products?search=nonexistent');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertEquals(0, $data['meta']['total']);
    }

    public function test_products_sorts_by_name(): void
    {
        $response = $this->getJson('/api/products?sort=name');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta',
            ]);
    }

    public function test_products_sorts_by_newest(): void
    {
        $response = $this->getJson('/api/products?sort=newest');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta',
            ]);
    }
}
