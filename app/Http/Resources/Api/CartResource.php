<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'items' => CartItemResource::collection($this->items),
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'shipping_estimate' => $this->shipping_estimate,
            'total' => $this->total,
            'coupon_code' => $this->coupon_code,
        ];
    }
}

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'thumbnail' => $this->thumbnail,
            'variant_id' => $this->variant_id,
            'variant_name' => $this->variant_name,
            'qty' => $this->quantity,
            'unit_price' => $this->unit_price,
            'subtotal' => $this->subtotal,
        ];
    }
}
