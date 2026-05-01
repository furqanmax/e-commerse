@blaze

@props([
    'collection',
    'reverse' => false,
    'size' => 'default', // default, large, medium
])

@php
    $thumbnailCollection = config('shopper.media.storage.thumbnail_collection');
    $image = $collection->getFirstMediaUrl($thumbnailCollection);
    $fallback = shopper_fallback_url();

    $aspectClass = match($size) {
        'large' => 'aspect-[16/10] lg:aspect-[16/12]',
        'medium' => 'aspect-[16/10]',
        default => 'aspect-video sm:aspect-3/2',
    };

    $titleClass = match($size) {
        'large' => 'text-2xl lg:text-3xl xl:text-4xl',
        'medium' => 'text-xl lg:text-2xl',
        default => 'text-lg',
    };

    $paddingClass = match($size) {
        'large' => 'p-6 lg:p-8 xl:p-10',
        'medium' => 'p-5 lg:p-6',
        default => 'p-6',
    };
@endphp

<x-link
    :href="route('shop.collection', $collection)"
    {{ $attributes->twMerge(['class' => 'group relative block overflow-hidden rounded-2xl lg:rounded-3xl bg-zinc-100 dark:bg-zinc-800 hover-lift h-full']) }}
>
    <div class="relative {{ $aspectClass }} h-full">
        <img
            src="{{ $image ?: $fallback }}"
            alt="{{ $collection->name }}"
            class="absolute inset-0 size-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105"
            loading="lazy"
        />

        <!-- Enhanced Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/90 via-zinc-950/40 to-transparent opacity-80 group-hover:opacity-90 transition-opacity duration-500"></div>

        <!-- Optional Pattern Overlay -->
        <div class="absolute inset-0 opacity-5 bg-[radial-gradient(circle_at_1px_1px,white_1px,transparent_0)] bg-[length:20px_20px]"></div>
    </div>

    <!-- Content -->
    <div class="absolute inset-x-0 bottom-0 {{ $paddingClass }}">
        <!-- Collection Label -->
        <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm text-xs font-medium text-white/80 mb-3">
            {{ __('Collection') }}
        </span>

        <!-- Title -->
        <h3 class="{{ $titleClass }} font-bold text-white font-heading tracking-tight leading-tight">
            {{ $collection->name }}
        </h3>

        <!-- Description -->
        @if ($collection->description)
            <p class="mt-2 text-sm text-zinc-300 line-clamp-2 max-w-lg">
                {{ strip_tags($collection->description) }}
            </p>
        @endif

        <!-- CTA -->
        <div class="mt-4 lg:mt-6 flex items-center gap-2 text-white group-hover:text-zinc-200 transition-colors">
            <span class="text-sm font-medium">{{ __('Explore Collection') }}</span>
            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </div>
    </div>

    <!-- Product Count Badge (if available) -->
    @if (isset($collection->products_count))
        <div class="absolute top-4 right-4 lg:top-6 lg:right-6">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-sm text-xs font-medium text-white">
                {{ $collection->products_count }} {{ __('items') }}
            </span>
        </div>
    @endif
</x-link>
