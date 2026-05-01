@blaze

@props([
    'product',
])

@php
    $thumbnailCollection = config('shopper.media.storage.thumbnail_collection');
    $image = $product->getFirstMediaUrl($thumbnailCollection);
    $fallback = shopper_fallback_url();
    $price = $product->getFormattedPrice();
@endphp

<div {{ $attributes->twMerge(['class' => 'group relative flex flex-col']) }}>
    <!-- Image Container -->
    <div class="relative aspect-square overflow-hidden rounded-2xl lg:rounded-3xl bg-zinc-100 dark:bg-zinc-800 hover-lift">
        <img
            src="{{ $image ?: $fallback }}"
            alt="{{ $product->name }}"
            class="size-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
            loading="lazy"
        />

        <!-- Discount Badge -->
        @if ($price?->percentage && $price->percentage > 0)
            <span class="absolute top-3 left-3 inline-flex items-center rounded-full bg-zinc-900 dark:bg-white px-3 py-1 text-xs font-semibold text-white dark:text-zinc-900">
                -{{ $price->percentage }}%
            </span>
        @endif

        <!-- Quick Action Overlay -->
        <div class="absolute inset-x-0 bottom-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out">
            <button class="w-full py-3 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-sm rounded-xl text-sm font-medium text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors shadow-lg">
                Quick View
            </button>
        </div>
    </div>

    <!-- Product Info -->
    <div class="mt-4 flex flex-col flex-1">
        <!-- Brand -->
        @if ($product->brand_id && $product->relationLoaded('brand'))
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">
                {{ $product->brand->name }}
            </p>
        @endif

        <!-- Name -->
        <h3 class="mt-1 text-sm lg:text-base font-medium text-zinc-900 dark:text-white group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors line-clamp-2">
            <x-link :href="route('shop.product', $product)" class="focus:outline-none">
                <span class="absolute inset-0" aria-hidden="true"></span>
                {{ $product->name }}
            </x-link>
        </h3>

        <!-- Price -->
        @if (! $product->isVariant() && $product->prices->isNotEmpty())
            <div class="mt-2 flex items-center gap-2">
                @if ($price->compare)
    <span class="text-base font-semibold text-zinc-900 dark:text-white">
        {{ $price->amount->formatted }}
    </span>
    <span class="text-sm text-zinc-500 line-through">
        {{ $price->compare->formatted }}
    </span>
@else
    <span class="text-base font-semibold text-zinc-900 dark:text-white">
        {{ $price->amount->formatted }}
    </span>
@endif
            </div>
        @endif
    </div>
</div>
