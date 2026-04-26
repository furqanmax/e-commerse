<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shopper\Core\Enum\OrderStatus;
use Shopper\Core\Enum\ShippingStatus;
use Shopper\Core\Models\Order;
use Shopper\Core\Models\OrderItem;

class OrderController extends Controller
{
    public function index(Request $request): OrderCollection
    {
        $perPage = min((int) $request->query('per_page', 10), 20);

        $query = Order::query()
            ->where('customer_id', $request->user()->id)
            ->with(['items', 'shippingOption', 'paymentMethod'])
            ->orderByDesc('created_at');

        if ($request->has('status')) {
            $status = $request->query('status');
            $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

            if (in_array($status, $validStatuses, true)) {
                $query->where('status', $this->mapStatusToOrderStatus($status));
            }
        }

        $orders = $query->paginate($perPage);

        return new OrderCollection($orders);
    }

    public function show(Request $request, string $orderNumber): JsonResponse
    {
        $order = Order::where('number', $orderNumber)
            ->with(['items.product', 'shippingAddress', 'shippingOption', 'paymentMethod', 'shippings.events'])
            ->first();

        if (! $order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        if ($order->customer_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'data' => new OrderResource($order),
        ]);
    }

    public function cancel(Request $request, string $orderNumber): JsonResponse
    {
        $order = Order::where('number', $orderNumber)
            ->with(['items'])
            ->first();

        if (! $order) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }

        if ($order->customer_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $cancellableStatuses = [OrderStatus::New, OrderStatus::Processing];
        $cancellableShippingStatuses = [
            ShippingStatus::Unfulfilled,
            ShippingStatus::PartiallyShipped,
        ];

        if (! in_array($order->status, $cancellableStatuses, true)) {
            return response()->json([
                'message' => 'Order cannot be cancelled in its current status',
            ], 422);
        }

        if (! in_array($order->shipping_status, $cancellableShippingStatuses, true)) {
            return response()->json([
                'message' => 'Order cannot be cancelled as it has been shipped or delivered',
            ], 422);
        }

        $this->restockItems($order);

        $order->update([
            'status' => OrderStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'data' => new OrderResource($order->fresh(['items', 'shippingAddress', 'shippingOption', 'paymentMethod', 'shippings.events'])),
            'message' => 'Order cancelled successfully',
        ]);
    }

    protected function mapStatusToOrderStatus(string $status): OrderStatus
    {
        return match ($status) {
            'pending' => OrderStatus::New,
            'processing' => OrderStatus::Processing,
            'shipped', 'delivered' => OrderStatus::Completed,
            'cancelled' => OrderStatus::Cancelled,
            default => OrderStatus::New,
        };
    }

    protected function restockItems(Order $order): void
    {
        $order->items
            ->filter(fn (OrderItem $item) => $item->product)
            ->each(function (OrderItem $item): void {
                $inventory = DB::table('sh_inventory_histories')->insert([
                    'inventory_id' => $this->getDefaultInventoryId(),
                    'stockable_type' => $item->product_type,
                    'stockable_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'event' => 'order_cancelled',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    protected function getDefaultInventoryId(): int
    {
        $inventory = DB::table('sh_inventories')->where('is_default', true)->first();

        return $inventory?->id ?? 1;
    }
}
