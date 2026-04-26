<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Shopper\Core\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            if (! $this->tablesExist(['categories'])) {
                return response()->json(['data' => [], 'message' => 'Categories unavailable']);
            }

            $categories = Cache::remember('api_categories_tree', 300, function () {
                return $this->getCategoryTree();
            });

            return response()->json([
                'data' => CategoryResource::collection($categories),
            ]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'message' => 'Categories unavailable']);
        }
    }

    public function store(): JsonResponse
    {
        return response()->json(['data' => [], 'message' => 'coming soon'], 200);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => [], 'message' => 'coming soon'], 200);
    }

    public function update(): JsonResponse
    {
        return response()->json(['data' => [], 'message' => 'coming soon'], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json(['data' => [], 'message' => 'coming soon'], 200);
    }

    protected function getCategoryTree(): Collection
    {
        return Category::query()
            ->whereNull('parent_id')
            ->enabled()
            ->withCount([
                'products' => fn ($q) => $q->where('is_visible', true),
            ])
            ->with([
                'children' => function ($q) {
                    $q->enabled()
                        ->withCount([
                            'products' => fn ($q) => $q->where('is_visible', true),
                        ])
                        ->with([
                            'children' => function ($q) {
                                $q->enabled()
                                    ->withCount([
                                        'products' => fn ($q) => $q->where('is_visible', true),
                                    ])
                                    ->orderBy('position');
                            },
                        ])
                        ->orderBy('position');
                },
            ])
            ->orderBy('position')
            ->get();
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
