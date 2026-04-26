<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->when($this->description !== null, $this->description),
            'image_url' => $this->getFirstMediaUrl(config('shopper.media.storage.thumbnail_collection')),
            'product_count' => $this->when(
                $this->products_count !== null,
                fn () => (int) $this->products_count
            ),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
