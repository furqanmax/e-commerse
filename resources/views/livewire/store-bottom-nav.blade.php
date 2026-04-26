<nav 
    x-data="{ loading: null }" 
    class="fixed bottom-0 left-0 right-0 bg-white border-t border-zinc-200 dark:bg-zinc-900 dark:border-white/10 z-40 lg:hidden"
>
    <div class="grid grid-cols-5 gap-1 px-2 py-2">
        <!-- Home -->
        <button
            @click="loading = 'home'; window.location.href = '{{ route('shop.index') }}'"
            @class([
                'flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-all duration-200 transform active:scale-95',
                'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' => request()->routeIs('shop.index'),
                'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800' => !request()->routeIs('shop.index'),
            ])
        >
            <div x-show="loading !== 'home'" class="flex flex-col items-center">
                <x-flux::icon.home variant="outline" class="size-5 mb-1" />
                <span class="text-xs">{{ __('Home') }}</span>
            </div>
            <div x-show="loading === 'home'" x-cloak class="flex flex-col items-center">
                <div class="size-5 mb-1 animate-spin">
                    <x-flux::icon.arrow-path variant="outline" class="size-5" />
                </div>
                <span class="text-xs">{{ __('Loading') }}</span>
            </div>
        </button>

        <!-- Categories -->
        <button
            @click="loading = 'categories'; window.location.href = '{{ route('shop.categories') }}'"
            @class([
                'flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-all duration-200 transform active:scale-95',
                'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' => request()->routeIs('shop.categories'),
                'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800' => !request()->routeIs('shop.categories'),
            ])
        >
            <div x-show="loading !== 'categories'" class="flex flex-col items-center">
                <x-flux::icon.squares-2x2 variant="outline" class="size-5 mb-1" />
                <span class="text-xs">{{ __('Categories') }}</span>
            </div>
            <div x-show="loading === 'categories'" x-cloak class="flex flex-col items-center">
                <div class="size-5 mb-1 animate-spin">
                    <x-flux::icon.arrow-path variant="outline" class="size-5" />
                </div>
                <span class="text-xs">{{ __('Loading') }}</span>
            </div>
        </button>

        <!-- Search -->
        <button
            @click="loading = 'search'; window.location.href = '{{ route('shop.search') }}'"
            @class([
                'flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-all duration-200 transform active:scale-95',
                'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' => request()->routeIs('shop.search'),
                'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800' => !request()->routeIs('shop.search'),
            ])
        >
            <div x-show="loading !== 'search'" class="flex flex-col items-center">
                <x-flux::icon.magnifying-glass variant="outline" class="size-5 mb-1" />
                <span class="text-xs">{{ __('Search') }}</span>
            </div>
            <div x-show="loading === 'search'" x-cloak class="flex flex-col items-center">
                <div class="size-5 mb-1 animate-spin">
                    <x-flux::icon.arrow-path variant="outline" class="size-5" />
                </div>
                <span class="text-xs">{{ __('Loading') }}</span>
            </div>
        </button>

        <!-- Orders -->
        <button
            @click="loading = 'orders'; window.location.href = '{{ auth()->check() ? route('account.orders') : route('login') }}'"
            @class([
                'flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-all duration-200 transform active:scale-95',
                'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' => request()->routeIs('account.orders'),
                'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800' => !request()->routeIs('account.orders'),
            ])
        >
            <div x-show="loading !== 'orders'" class="flex flex-col items-center">
                <x-flux::icon.archive-box variant="outline" class="size-5 mb-1" />
                <span class="text-xs">{{ __('Orders') }}</span>
            </div>
            <div x-show="loading === 'orders'" x-cloak class="flex flex-col items-center">
                <div class="size-5 mb-1 animate-spin">
                    <x-flux::icon.arrow-path variant="outline" class="size-5" />
                </div>
                <span class="text-xs">{{ __('Loading') }}</span>
            </div>
        </button>

        <!-- Cart -->
        <button
            @click="loading = 'cart'; window.location.href = '{{ route('shop.cart') }}'"
            @class([
                'flex flex-col items-center justify-center py-2 px-1 rounded-lg transition-all duration-200 transform active:scale-95 relative',
                'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20' => request()->routeIs('shop.cart'),
                'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800' => !request()->routeIs('shop.cart'),
            ])
        >
            <div x-show="loading !== 'cart'" class="flex flex-col items-center">
                <x-flux::icon.shopping-cart variant="outline" class="size-5 mb-1" />
                <livewire:cart-count />
                <span class="text-xs">{{ __('Cart') }}</span>
            </div>
            <div x-show="loading === 'cart'" x-cloak class="flex flex-col items-center">
                <div class="size-5 mb-1 animate-spin">
                    <x-flux::icon.arrow-path variant="outline" class="size-5" />
                </div>
                <span class="text-xs">{{ __('Loading') }}</span>
            </div>
        </button>
    </div>
</nav>