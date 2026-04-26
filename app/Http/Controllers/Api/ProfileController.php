<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Shopper\Core\Models\Product;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone_number,
                'avatar_url' => $user->picture,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
        ]);

        $user = $request->user();

        $updateData = [];
        if (array_key_exists('first_name', $validated)) {
            $updateData['first_name'] = $validated['first_name'];
        }
        if (array_key_exists('last_name', $validated)) {
            $updateData['last_name'] = $validated['last_name'];
        }
        if (array_key_exists('phone', $validated)) {
            $updateData['phone_number'] = $validated['phone'];
        }

        $user->update($updateData);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone_number,
                'avatar_url' => $user->picture,
            ],
            'message' => 'Profile updated successfully.',
        ]);
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'avatar' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
        ]);

        $user = $request->user();

        $path = $validated['avatar']->store(
            'avatars',
            config('shopper.media.storage.disk_name')
        );

        $user->update([
            'avatar_type' => 'storage',
            'avatar_location' => $path,
        ]);

        return response()->json([
            'data' => [
                'avatar_url' => $user->picture,
            ],
            'message' => 'Avatar uploaded successfully.',
        ]);
    }

    public function updateEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided password is incorrect.',
            ], 422);
        }

        $user->update(['email' => $validated['email']]);

        return response()->json([
            'data' => [
                'email' => $user->email,
            ],
            'message' => 'Email updated successfully.',
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'The current password is incorrect.',
            ], 422);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return response()->json([
            'message' => 'Password updated successfully.',
        ]);
    }

    public function getWishlist(Request $request): JsonResponse
    {
        $user = $request->user();

        $wishlist = Wishlist::where('user_id', $user->id)
            ->with(['product'])
            ->get()
            ->map(function (Wishlist $item) {
                if (! $item->product) {
                    return null;
                }

                return [
                    'id' => $item->product_id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'price' => $item->product->price?->amount / 100 ?? 0,
                    'thumbnail' => $item->product->getFirstMediaUrl(
                        config('shopper.media.storage.thumbnail_collection')
                    ),
                    'is_visible' => $item->product->is_visible,
                    'created_at' => $item->created_at->toIso8601String(),
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'data' => $wishlist,
        ]);
    }

    public function toggleWishlist(Request $request, int $productId): JsonResponse
    {
        $user = $request->user();

        $product = Product::find($productId);

        if (! $product) {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        }

        $wishlistItem = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            $wishlisted = false;
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
            $wishlisted = true;
        }

        return response()->json([
            'wishlisted' => $wishlisted,
        ]);
    }
}
