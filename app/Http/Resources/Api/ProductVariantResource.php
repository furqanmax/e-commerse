<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currency = current_currency();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => $this->getPriceAmount($currency),
            'compare_price' => $this->getComparePriceAmount($currency),
            'stock_qty' => $this->stock,
            'in_stock' => $this->isInStock(),
            'options' => $this->values->map(fn ($value) => [
                'attribute' => $value->attribute->name ?? null,
                'value' => $value->value,
            ]),
        ];
    }
}
