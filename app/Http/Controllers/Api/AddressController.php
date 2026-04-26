<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Shopper\Core\Models\Address;
use Shopper\Core\Models\Country;

class AddressController extends Controller
{
    private const MAX_ADDRESSES = 5;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $addresses = Address::where('user_id', $user->id)
            ->orderBy('shipping_default', 'desc')
            ->orderBy('billing_default', 'desc')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $addresses->map(fn ($address) => $this->transformAddress($address)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $existingCount = Address::where('user_id', $user->id)->count();
        if ($existingCount >= self::MAX_ADDRESSES) {
            return response()->json([
                'message' => 'Maximum of '.self::MAX_ADDRESSES.' addresses allowed.',
                'errors' => ['address' => ['You have reached the maximum number of addresses.']],
            ], 422);
        }

        $validator = $this->validateAddress($request);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $isDefault = $existingCount === 0;
        if ($isDefault) {
            Address::where('user_id', $user->id)
                ->where('shipping_default', true)
                ->update(['shipping_default' => false]);
        }

        $country = Country::where('cca2', $data['country_code'])->first();

        $address = Address::create([
            'user_id' => $user->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone'] ?? null,
            'street_address' => $data['address1'],
            'street_address_plus' => $data['address2'] ?? null,
            'city' => $data['city'],
            'state' => $data['state'],
            'postal_code' => $data['postcode'],
            'country_id' => $country?->id,
            'shipping_default' => $isDefault,
            'billing_default' => false,
            'type' => 'shipping',
        ]);

        return response()->json([
            'data' => $this->transformAddress($address),
            'message' => 'Address created successfully.',
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $address = Address::find($id);

        if (! $address || $address->user_id !== $user->id) {
            return response()->json([
                'message' => 'Address not found.',
            ], 404);
        }

        return response()->json([
            'data' => $this->transformAddress($address),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $address = Address::find($id);

        if (! $address || $address->user_id !== $user->id) {
            return response()->json([
                'message' => 'Address not found or unauthorized.',
            ], 404);
        }

        $validator = $this->validateAddress($request, $id);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $country = Country::where('cca2', $data['country_code'])->first();

        $address->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone'] ?? null,
            'street_address' => $data['address1'],
            'street_address_plus' => $data['address2'] ?? null,
            'city' => $data['city'],
            'state' => $data['state'],
            'postal_code' => $data['postcode'],
            'country_id' => $country?->id,
        ]);

        return response()->json([
            'data' => $this->transformAddress($address->fresh()),
            'message' => 'Address updated successfully.',
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $address = Address::find($id);

        if (! $address || $address->user_id !== $user->id) {
            return response()->json([
                'message' => 'Address not found or unauthorized.',
            ], 404);
        }

        $wasDefault = $address->shipping_default;
        $address->delete();

        if ($wasDefault) {
            $nextDefault = Address::where('user_id', $user->id)
                ->orderByDesc('id')
                ->first();

            if ($nextDefault) {
                $nextDefault->update(['shipping_default' => true]);
            }
        }

        return response()->json([
            'message' => 'Address deleted successfully.',
        ]);
    }

    public function setDefault(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $address = Address::find($id);

        if (! $address || $address->user_id !== $user->id) {
            return response()->json([
                'message' => 'Address not found or unauthorized.',
            ], 404);
        }

        Address::where('user_id', $user->id)
            ->where('shipping_default', true)
            ->update(['shipping_default' => false]);

        $address->update(['shipping_default' => true]);

        return response()->json([
            'data' => $this->transformAddress($address->fresh()),
            'message' => 'Default address updated successfully.',
        ]);
    }

    protected function validateAddress(Request $request, ?int $addressId = null): \Illuminate\Validation\Validator
    {
        $validCountryCodes = Country::pluck('cca2')->toArray();

        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address1' => ['required', 'string', 'max:255'],
            'address2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postcode' => ['required', 'string', 'max:20'],
            'country_code' => [
                'required',
                'string',
                'size:2',
                Rule::in($validCountryCodes),
            ],
        ];

        if ($addressId) {
            $rules['first_name'] = ['sometimes', 'required', 'string', 'max:100'];
            $rules['last_name'] = ['sometimes', 'required', 'string', 'max:100'];
            $rules['address1'] = ['sometimes', 'required', 'string', 'max:255'];
            $rules['city'] = ['sometimes', 'required', 'string', 'max:100'];
            $rules['state'] = ['sometimes', 'required', 'string', 'max:100'];
            $rules['postcode'] = ['sometimes', 'required', 'string', 'max:20'];
            $rules['country_code'] = [
                'sometimes',
                'required',
                'string',
                'size:2',
                Rule::in($validCountryCodes),
            ];
        }

        return Validator::make($request->all(), $rules, [
            'country_code.in' => 'Invalid country code. Use ISO 3166-1 alpha-2 format.',
        ]);
    }

    protected function transformAddress(Address $address): array
    {
        $country = $address->country;

        return [
            'id' => $address->id,
            'first_name' => $address->first_name,
            'last_name' => $address->last_name,
            'full_name' => $address->first_name.' '.$address->last_name,
            'phone' => $address->phone_number,
            'address1' => $address->street_address,
            'address2' => $address->street_address_plus,
            'city' => $address->city,
            'state' => $address->state,
            'postcode' => $address->postal_code,
            'country_code' => $country?->cca2,
            'country_name' => $country?->name,
            'is_default' => $address->shipping_default,
            'formatted' => implode(', ', array_filter([
                $address->street_address,
                $address->street_address_plus,
                $address->city,
                $address->state,
                $address->postal_code,
                $country?->name,
            ])),
        ];
    }
}
