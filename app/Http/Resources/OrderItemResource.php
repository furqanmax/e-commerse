<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price_amount / 100,
            'total' => $this->total / 100,
            'thumbnail' => $this->getItemThumbnail(),
        ];
    }

    protected function getItemThumbnail(): ?string
    {
        if (! $this->product) {
            return null;
        }

        return $this->product->getFirstMediaUrl(
            config('shopper.media.storage.thumbnail_collection')
        );
    }
}
