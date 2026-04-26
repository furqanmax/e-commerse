<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductCollection;
use App\Http\Resources\Api\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shopper\Core\Models\Product;
use Shopper\Feature;

class ProductController extends Controller
{
    public function index(Request $request): ProductCollection|JsonResponse
    {
        try {
            $perPage = min((int) $request->input('per_page', 15), 30);

            if (! $this->tablesExist(['products'])) {
                return response()->json([
                    'data' => [],
                    'meta' => ['current_page' => 1, 'last_page' => 1, 'per_page' => $perPage, 'total' => 0],
                ]);
            }

            $currency = $this->getCurrency();

            $query = Product::query()
                ->publish()
                ->withCurrentPrices();

            if ($this->reviewsEnabled()) {
                $query->withCount(['reviews']);
            }

            if ($request->filled('category_slug')) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('slug', $request->input('category_slug'));
                });
            }

            if ($request->filled('brand_id')) {
                $query->where('brand_id', $request->input('brand_id'));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');

                if ($this->scoutEnabled()) {
                    $ids = $this->scoutSearch($search);
                    if (! empty($ids)) {
                        $query->whereIn('id', $ids);
                    }
                } else {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
                }
            }

            $sort = $request->input('sort', 'newest');
            $query = $this->applySorting($query, $sort, $currency);

            $minPrice = $request->input('min_price');
            $maxPrice = $request->input('max_price');

            if ($minPrice !== null || $maxPrice !== null) {
                try {
                    $currencyId = DB::table(shopper_table('currencies'))
                        ->where('code', $currency)
                        ->value('id');

                    if ($minPrice !== null) {
                        $query->whereHas('prices', fn ($q) => $q
                            ->where('currency_id', $currencyId)
                            ->where('amount', '>=', (int) $minPrice * 100));
                    }
                    if ($maxPrice !== null) {
                        $query->whereHas('prices', fn ($q) => $q
                            ->where('currency_id', $currencyId)
                            ->where('amount', '<=', (int) $maxPrice * 100));
                    }
                } catch (\Exception $e) {
                    // Skip price filtering if currencies table doesn't exist
                }
            }

            return new ProductCollection(
                $query->paginate($perPage)
            );
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'meta' => ['current_page' => 1, 'last_page' => 1, 'per_page' => 15, 'total' => 0],
            ]);
        }
    }

    public function store(): JsonResponse
    {
        return response()->json(['data' => [], 'message' => 'coming soon'], 200);
    }

    public function show(string $slug): ProductResource|JsonResponse
    {
        try {
            $currency = $this->getCurrency();

            $relations = [
                'brand',
                'categories',
                'variants',
                'variants.values.attribute',
                'variants.prices' => fn ($q) => $q->whereRelation('currency', 'code', $currency),
                'prices' => fn ($q) => $q->whereRelation('currency', 'code', $currency),
                'relatedProducts' => fn ($q) => $q->publish()->limit(4),
                'relatedProducts.prices' => fn ($q) => $q->whereRelation('currency', 'code', $currency),
            ];

            if ($this->reviewsEnabled()) {
                $relations[] = 'reviews';
            }

            $query = Product::query()
                ->where('slug', $slug)
                ->with($relations);

            if ($this->reviewsEnabled()) {
                $query->withCount(['reviews']);
            }

            $product = $query->first();

            if (! $product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            return new ProductResource($product);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function update(): JsonResponse
    {
        return response()->json(['data' => [], 'message' => 'coming soon'], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json(['data' => [], 'message' => 'coming soon'], 200);
    }

    protected function getCurrency(): string
    {
        try {
            return current_currency();
        } catch (\Exception $e) {
            return 'USD';
        }
    }

    protected function applySorting($query, string $sort, string $currency): mixed
    {
        if (! in_array($sort, ['price_asc', 'price_desc'])) {
            return $sort === 'name' ? $query->orderBy('name') : $query->orderByDesc('created_at');
        }

        try {
            $currencyId = DB::table(shopper_table('currencies'))
                ->where('code', $currency)
                ->value('id');
        } catch (\Exception $e) {
            return $query->orderByDesc('created_at');
        }

        $priceColumn = shopper_table('product_prices').'.amount';

        return match ($sort) {
            'price_asc' => $query
                ->select(shopper_table('products').'.*')
                ->leftJoin(
                    shopper_table('product_prices'),
                    fn ($j) => $j
                        ->on(shopper_table('products.id'), '=', shopper_table('product_prices.product_id'))
                        ->where(shopper_table('product_prices.currency_id'), $currencyId)
                )
                ->orderByRaw("COALESCE({$priceColumn}, 0) ASC"),
            'price_desc' => $query
                ->select(shopper_table('products').'.*')
                ->leftJoin(
                    shopper_table('product_prices'),
                    fn ($j) => $j
                        ->on(shopper_table('products.id'), '=', shopper_table('product_prices.product_id'))
                        ->where(shopper_table('product_prices.currency_id'), $currencyId)
                )
                ->orderByRaw("COALESCE({$priceColumn}, 0) DESC"),
            default => $query->orderByDesc('created_at'),
        };
    }

    protected function reviewsEnabled(): bool
    {
        try {
            return Feature::enabled('review')
                && DB::table(shopper_table('reviews'))->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function scoutEnabled(): bool
    {
        return interface_exists('\Laravel\Scout\Searchable');
    }

    protected function scoutSearch(string $query): array
    {
        if (! $this->scoutEnabled()) {
            return [];
        }

        try {
            return Product::search($query)
                ->limit(100)
                ->get()
                ->pluck('id')
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function tablesExist(array $tables): bool
    {
        foreach ($tables as $table) {
            try {
                $tableName = shopper_table($table);
                DB::table($tableName)->limit(1)->get();
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }
}
