<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currency = current_currency();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->when($this->description !== null, $this->description),
            'summary' => $this->when($this->summary !== null, $this->summary),
            'sku' => $this->when($this->sku !== null, $this->sku),
            'price' => $this->getPrice(),
            'compare_price' => $this->getComparePrice(),
            'thumbnail' => $this->getThumbnail(),
            'images' => $this->when(
                $request->routeIs('api.products.show'),
                fn () => $this->getImages()
            ),
            'in_stock' => $this->getStock() > 0,
            'stock_quantity' => $this->getStock(),
            'average_rating' => $this->when(
                $this->relationLoaded('reviews'),
                fn () => $this->reviews->avg('rating') ?? 0
            ),
            'review_count' => $this->when(
                $this->relationLoaded('reviews'),
                fn () => $this->reviews->count()
            ),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'category' => CategoryResource::collection($this->whenLoaded('categories')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'related_products' => ProductResource::collection($this->whenLoaded('relatedProducts')),
            'is_featured' => $this->featured ?? false,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    protected function getPrice(): ?float
    {
        if (method_exists($this->resource, 'getPriceAmount')) {
            return $this->getPriceAmount(current_currency());
        }

        $price = $this->prices->firstWhere('currency.code', current_currency());

        return $price ? $price->amount / 100 : null;
    }

    protected function getComparePrice(): ?float
    {
        if (method_exists($this->resource, 'getComparePriceAmount')) {
            return $this->getComparePriceAmount(current_currency());
        }

        $price = $this->prices->firstWhere('currency.code', current_currency());

        return $price && $price->compare_amount ? $price->compare_amount / 100 : null;
    }

    protected function getThumbnail(): ?string
    {
        if (method_exists($this->resource, 'getFirstMediaUrl')) {
            return $this->getFirstMediaUrl(config('shopper.media.storage.thumbnail_collection', 'thumbnails'));
        }

        $media = $this->media->first();

        return $media ? asset('storage/'.$media->id.'/'.$media->file_name) : null;
    }

    protected function getImages(): array
    {
        if (method_exists($this->resource, 'getMediaUrls')) {
            return $this->getMediaUrls(config('shopper.media.storage.collection_name', 'media'));
        }

        return $this->media->map(fn ($m) => asset('storage/'.$m->id.'/'.$m->file_name))->toArray();
    }
}
