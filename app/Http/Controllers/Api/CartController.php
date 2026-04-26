<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected function getCartService(Request $request): CartService
    {
        $user = $request->user();
        $guestId = $request->header('X-Guest-Id');

        $cartService = new CartService($user);

        if (! $user && $guestId) {
            $cartService->setGuestId($guestId);
        }

        return $cartService->resolveCart();
    }

    public function index(Request $request): JsonResponse
    {
        $cartService = $this->getCartService($request);

        return response()->json($cartService->toArray());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'variant_id' => 'nullable|integer',
            'qty' => 'required|integer|min:1',
            'guest_id' => 'nullable|string',
        ]);

        $cartService = $this->getCartService($request);

        if (! $request->user() && ! $request->hasHeader('X-Guest-Id') && ($validated['guest_id'] ?? null)) {
            $cartService->setGuestId($validated['guest_id']);
            $cartService->resolveCart();
        }

        $result = $cartService->addItem(
            $validated['product_id'],
            $validated['qty'],
            $validated['variant_id'] ?? null
        );

        if (! $result['success']) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json($cartService->toArray());
    }

    public function show(Request $request): JsonResponse
    {
        return $this->index($request);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $cartService = $this->getCartService($request);

        $result = $cartService->updateItem($id, $validated['qty']);

        if (! $result['success']) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json($cartService->toArray());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $cartService = $this->getCartService($request);

        $removed = $cartService->removeItem($id);

        if (! $removed) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        return response()->json($cartService->toArray());
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        $cartService = $this->getCartService($request);

        $result = $cartService->applyCoupon($validated['code']);

        if (! $result['success']) {
            return response()->json(['message' => $result['error']], $result['code']);
        }

        return response()->json($cartService->toArray());
    }

    public function removeCoupon(Request $request): JsonResponse
    {
        $cartService = $this->getCartService($request);

        $cartService->removeCoupon();

        return response()->json($cartService->toArray());
    }

    public function merge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'guest_id' => 'required|string',
        ]);

        $cartService = $this->getCartService($request);

        $cartService->mergeGuestCart($validated['guest_id']);

        return response()->json($cartService->toArray());
    }
}
