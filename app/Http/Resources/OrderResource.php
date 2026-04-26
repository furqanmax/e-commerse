<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->number,
            'status' => $this->status->value,
            'payment_status' => $this->payment_status->value,
            'shipping_status' => $this->shipping_status->value,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'total' => $this->total(),
            'currency' => $this->currency_code,
            'items_count' => $this->items->count(),
            'thumbnail' => $this->getFirstItemThumbnail(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'shipping_address' => new OrderAddressResource($this->whenLoaded('shippingAddress')),
            'shipping_method' => $this->when($this->shippingOption, fn () => [
                'id' => $this->shippingOption->id,
                'name' => $this->shippingOption->name,
                'price' => $this->shippingOption->pivot?->price_amount ?? 0,
            ]),
            'tracking_number' => $this->getTrackingNumber(),
            'tracking_url' => $this->getTrackingUrl(),
            'payment_method' => $this->when($this->paymentMethod, fn () => [
                'id' => $this->paymentMethod->id,
                'name' => $this->paymentMethod->title,
            ]),
            'timeline' => $this->when($this->relationLoaded('shippings'), function () {
                return $this->buildTimeline();
            }),
        ];
    }

    protected function getFirstItemThumbnail(): ?string
    {
        $firstItem = $this->items->first();

        if (! $firstItem || ! $firstItem->product) {
            return null;
        }

        $media = $firstItem->product->getFirstMediaUrl(
            config('shopper.media.storage.thumbnail_collection')
        );

        return $media ?: null;
    }

    protected function getTrackingNumber(): ?string
    {
        $shipping = $this->shippings->first();

        return $shipping?->tracking_number;
    }

    protected function getTrackingUrl(): ?string
    {
        $shipping = $this->shippings->first();

        return $shipping?->tracking_url;
    }

    protected function buildTimeline(): array
    {
        $timeline = [];

        $timeline[] = [
            'status' => 'created',
            'timestamp' => $this->created_at->toIso8601String(),
            'note' => 'Order placed',
        ];

        if ($this->status->value === 'processing') {
            $timeline[] = [
                'status' => 'processing',
                'timestamp' => $this->updated_at->toIso8601String(),
                'note' => 'Order is being processed',
            ];
        }

        if ($this->status->value === 'completed') {
            $timeline[] = [
                'status' => 'completed',
                'timestamp' => $this->updated_at->toIso8601String(),
                'note' => 'Order completed',
            ];
        }

        if ($this->status->value === 'cancelled') {
            $timeline[] = [
                'status' => 'cancelled',
                'timestamp' => $this->cancelled_at?->toIso8601String() ?? $this->updated_at->toIso8601String(),
                'note' => 'Order cancelled',
            ];
        }

        foreach ($this->shippings as $shipping) {
            foreach ($shipping->events as $event) {
                $timeline[] = [
                    'status' => $event->status->value,
                    'timestamp' => $event->occurred_at->toIso8601String(),
                    'note' => $event->description,
                ];
            }
        }

        usort($timeline, fn ($a, $b) => $a['timestamp'] <=> $b['timestamp']);

        return $timeline;
    }
}
