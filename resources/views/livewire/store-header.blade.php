<header
    x-data="{ mobileOpen: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 20)"
    :class="{
        'bg-white/90 dark:bg-[#0a0a0a]/90 shadow-lg shadow-zinc-900/5': scrolled,
        'bg-white/50 dark:bg-[#0a0a0a]/50': !scrolled
    }"
    class="sticky top-0 z-50 border-b border-zinc-200/50 dark:border-white/5 backdrop-blur-xl transition-all duration-300"
>
    <x-announcement-bar :message="__('Summer Sale For All Swim Suits And Free Express Delivery - OFF 50%!')" />
    <x-container>
        <div class="flex items-center justify-between h-16 lg:h-20">
            <!-- Mobile: Logo + Menu Button -->
            <div class="flex items-center gap-4 lg:hidden">
                <button @click="mobileOpen = !mobileOpen" class="p-2 -ml-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <x-link :href="route('home')" class="flex items-center">
                    <img src="{{ asset('logo.png') }}" alt="Petkhazana" class="h-10 w-auto" />
                </x-link>
            </div>

            <!-- Desktop: Logo -->
            <x-link :href="route('home')" class="hidden lg:flex items-center">
                <img src="{{ asset('logo.png') }}" alt="Petkhazana" class="h-12 w-auto" />
            </x-link>

            <!-- Desktop: Navigation -->
            <nav role="navigation" class="hidden lg:flex items-center gap-8">
                @php
                    $navItems = [
                        ['href' => route('home'), 'label' => __('Home'), 'active' => request()->routeIs('home')],
                        ['href' => route('shop.index'), 'label' => __('Shop'), 'active' => request()->routeIs('shop.index')],
                        ['href' => route('shop.categories'), 'label' => __('Categories'), 'active' => request()->routeIs('shop.categories')],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    <x-link
                        :href="$item['href']"
                        @class([
                            'text-sm font-medium transition-colors relative after:absolute after:bottom-[-4px] after:left-0 after:h-[2px] after:w-0 after:bg-zinc-900 dark:after:bg-white after:transition-all hover:after:w-full',
                            'text-zinc-900 dark:text-white after:w-full' => $item['active'],
                            'text-zinc-500 hover:text-zinc-900 dark:hover:text-white' => ! $item['active'],
                        ])
                    >
                        {{ $item['label'] }}
                    </x-link>
                @endforeach
            </nav>

            <!-- Desktop: Actions -->
            <div class="hidden lg:flex items-center gap-2">
                <!-- Search -->
                <x-link
                    :href="route('shop.search')"
                    class="flex items-center justify-center w-10 h-10 rounded-full text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all"
                >
                    <span class="sr-only">{{ __('Search') }}</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </x-link>

                <!-- Account -->
                <x-link
                    :href="auth()->check() ? route('dashboard') : route('login')"
                    class="flex items-center justify-center w-10 h-10 rounded-full text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all"
                >
                    <span class="sr-only">{{ __('Account') }}</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </x-link>

                <!-- Cart -->
                <x-link
                    :href="route('shop.cart')"
                    class="relative flex items-center justify-center w-10 h-10 rounded-full text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all"
                >
                    <span class="sr-only">{{ __('Cart') }}</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <livewire:cart-count />
                </x-link>
            </div>

            <!-- Mobile: Cart Icon Only -->
            <x-link
                :href="route('shop.cart')"
                class="lg:hidden relative flex items-center justify-center w-10 h-10 text-zinc-600 dark:text-zinc-400"
            >
                <span class="sr-only">{{ __('Cart') }}</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <livewire:cart-count />
            </x-link>
        </div>
    </x-container>

    <!-- Mobile Menu -->
    <div
        x-show="mobileOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="lg:hidden absolute top-full left-0 right-0 bg-white dark:bg-[#0a0a0a] border-b border-zinc-200 dark:border-zinc-800 shadow-xl"
    >
        <div class="px-4 py-6 space-y-1">
            @php
                $mobileNavItems = [
                    ['href' => route('home'), 'label' => __('Home'), 'active' => request()->routeIs('home')],
                    ['href' => route('shop.index'), 'label' => __('Shop'), 'active' => request()->routeIs('shop.index')],
                    ['href' => route('shop.categories'), 'label' => __('Categories'), 'active' => request()->routeIs('shop.categories')],
                    ['href' => route('shop.search'), 'label' => __('Search'), 'active' => request()->routeIs('shop.search')],
                ];
            @endphp

            @foreach ($mobileNavItems as $item)
                <x-link
                    :href="$item['href']"
                    @class([
                        'flex items-center gap-3 px-4 py-3 rounded-xl text-base font-medium transition-colors',
                        'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' => $item['active'],
                        'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50' => ! $item['active'],
                    ])
                    @click="mobileOpen = false"
                >
                    {{ $item['label'] }}
                </x-link>
            @endforeach

            <div class="border-t border-zinc-200 dark:border-zinc-800 my-4"></div>

            @auth
                <x-link
                    :href="route('dashboard')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors"
                    @click="mobileOpen = false"
                >
                    {{ __('My Account') }}
                </x-link>
                <x-link
                    :href="route('account.orders')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors"
                    @click="mobileOpen = false"
                >
                    {{ __('My Orders') }}
                </x-link>
                <form method="POST" action="{{ route('logout') }}" class="px-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 py-3 text-base font-medium text-zinc-600 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 transition-colors text-left">
                        {{ __('Log Out') }}
                    </button>
                </form>
            @else
                <x-link
                    :href="route('login')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-medium text-zinc-900 dark:text-white bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors"
                    @click="mobileOpen = false"
                >
                    {{ __('Sign In') }}
                </x-link>
                <x-link
                    :href="route('register')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors"
                    @click="mobileOpen = false"
                >
                    {{ __('Create Account') }}
                </x-link>
            @endauth
        </div>
    </div>
</header>