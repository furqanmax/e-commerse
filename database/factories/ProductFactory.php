<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Shopper\Core\Enum\ProductType;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.$this->faker->unique()->randomNumber(5),
            'sku' => strtoupper($this->faker->unique()->bothify('??-####')),
            'description' => $this->faker->paragraphs(2, true),
            'summary' => $this->faker->sentence(),
            'type' => ProductType::Standard,
            'is_visible' => true,
            'featured' => false,
            'published_at' => now(),
            'brand_id' => null,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => now()->addDay(),
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_visible' => false,
        ]);
    }
}
