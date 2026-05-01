@blaze(fold: true)

<div {{ $attributes->twMerge(['class' => 'grid grid-cols-2 gap-8 lg:gap-12 sm:grid-cols-4']) }}>
    <!-- Free Shipping -->
    <div class="group flex flex-col items-center text-center">
        <div class="flex size-14 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800/50 ring-1 ring-zinc-200 dark:ring-zinc-700 transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-zinc-900/5 dark:group-hover:shadow-white/5">
            <svg class="w-6 h-6 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
            </svg>
        </div>
        <h3 class="mt-4 text-sm font-semibold text-zinc-900 dark:text-white font-heading">{{ __('Free Shipping') }}</h3>
        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('On orders over $50') }}</p>
    </div>

    <!-- Secure Payment -->
    <div class="group flex flex-col items-center text-center">
        <div class="flex size-14 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800/50 ring-1 ring-zinc-200 dark:ring-zinc-700 transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-zinc-900/5 dark:group-hover:shadow-white/5">
            <svg class="w-6 h-6 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>
        <h3 class="mt-4 text-sm font-semibold text-zinc-900 dark:text-white font-heading">{{ __('Secure Payment') }}</h3>
        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('100% secure checkout') }}</p>
    </div>

    <!-- Easy Returns -->
    <div class="group flex flex-col items-center text-center">
        <div class="flex size-14 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800/50 ring-1 ring-zinc-200 dark:ring-zinc-700 transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-zinc-900/5 dark:group-hover:shadow-white/5">
            <svg class="w-6 h-6 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </div>
        <h3 class="mt-4 text-sm font-semibold text-zinc-900 dark:text-white font-heading">{{ __('Easy Returns') }}</h3>
        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('30-day return policy') }}</p>
    </div>

    <!-- 24/7 Support -->
    <div class="group flex flex-col items-center text-center">
        <div class="flex size-14 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800/50 ring-1 ring-zinc-200 dark:ring-zinc-700 transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-zinc-900/5 dark:group-hover:shadow-white/5">
            <svg class="w-6 h-6 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <h3 class="mt-4 text-sm font-semibold text-zinc-900 dark:text-white font-heading">{{ __('24/7 Support') }}</h3>
        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Dedicated support') }}</p>
    </div>
</div>
