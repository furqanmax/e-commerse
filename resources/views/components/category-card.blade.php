@blaze

@props([
    'category',
    'variant' => 'default', // default, featured
])

@php
    $thumbnailCollection = config('shopper.media.storage.thumbnail_collection');
    $image = $category->getFirstMediaUrl($thumbnailCollection);
    $fallback = shopper_fallback_url();
@endphp

@if ($variant === 'featured')
    <!-- Featured Variant - Image Focused Card -->
    <x-link
        :href="route('shop.category', $category)"
        {{ $attributes->twMerge(['class' => 'group relative block overflow-hidden rounded-2xl lg:rounded-3xl bg-zinc-100 dark:bg-zinc-800 hover-lift']) }}
    >
        <div class="relative aspect-[3/4] lg:aspect-[4/5]">
            <!-- Image -->
            <img
                src="{{ $image ?: $fallback }}"
                alt="{{ $category->name }}"
                class="absolute inset-0 w-full h-full object-cover img-zoom"
                loading="lazy"
            />

            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/80 via-zinc-900/20 to-transparent opacity-60 group-hover:opacity-70 transition-opacity duration-500"></div>

            <!-- Content Overlay -->
            <div class="absolute inset-0 p-4 lg:p-6 flex flex-col justify-end">
                <!-- Product Count Badge -->
                <span class="inline-flex items-center self-start px-2.5 py-1 rounded-full bg-white/20 backdrop-blur-sm text-xs font-medium text-white mb-3">
                    {{ $category->products_count ?? 0 }} {{ __('Products') }}
                </span>

                <!-- Category Name -->
                <h3 class="text-lg lg:text-xl font-semibold text-white font-heading tracking-tight">
                    {{ $category->name }}
                </h3>

                <!-- Hover Arrow -->
                <div class="mt-3 flex items-center gap-2 text-white/80 group-hover:text-white transition-colors">
                    <span class="text-sm font-medium opacity-0 group-hover:opacity-100 transform translate-x-[-10px] group-hover:translate-x-0 transition-all duration-300">
                        {{ __('Explore') }}
                    </span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
            </div>
        </div>
    </x-link>
@else
    <!-- Default Variant - Compact Circle -->
    <x-link
        :href="route('shop.category', $category)"
        {{ $attributes->twMerge(['class' => 'group relative flex flex-col items-center']) }}
    >
        <div class="relative size-24 lg:size-28 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800 ring-2 ring-transparent group-hover:ring-zinc-900 dark:group-hover:ring-white transition-all duration-300">
            <img
                src="{{ $image ?: $fallback }}"
                alt="{{ $category->name }}"
                class="size-full object-cover object-center transition-transform duration-500 group-hover:scale-110"
                loading="lazy"
            />
        </div>
        <span class="mt-3 text-sm text-center font-medium text-zinc-900 dark:text-white group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors">
            {{ $category->name }}
        </span>
    </x-link>
@endif
