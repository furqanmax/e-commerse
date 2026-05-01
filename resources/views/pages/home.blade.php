<div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)" class="relative">
    <!-- Hero Section - VengenceUI Inspired -->
    <section class="relative min-h-[85vh] lg:min-h-[90vh] overflow-hidden bg-white dark:bg-[#0a0a0a]">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.05]">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0); background-size: 40px 40px;"></div>
        </div>

        <!-- Gradient Orb -->
        <div class="absolute top-1/4 -right-64 w-[600px] h-[600px] bg-gradient-to-br from-zinc-200/50 to-transparent dark:from-zinc-800/30 dark:to-transparent rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-[400px] h-[400px] bg-gradient-to-tr from-zinc-100/80 to-transparent dark:from-zinc-800/20 dark:to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <x-container class="relative h-full">
            <div class="flex flex-col justify-center min-h-[85vh] lg:min-h-[90vh] py-12 lg:py-0">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                    <!-- Left Content -->
                    <div class="order-2 lg:order-1">
                        <!-- Badge -->
                        <div
                            x-show="loaded"
                            x-transition:enter="transition ease-out duration-700"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-zinc-100 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700/50 mb-8"
                        >
                            <span class="w-2 h-2 rounded-full bg-zinc-900 dark:bg-white animate-pulse"></span>
                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Premium Pet Products</span>
                        </div>

                        <!-- Main Heading with Animation -->
                        <h1
                            x-show="loaded"
                            x-transition:enter="transition ease-out duration-1000 delay-200"
                            x-transition:enter-start="opacity-0 translate-y-8"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-bold tracking-tight text-zinc-900 dark:text-white font-heading leading-[0.95]"
                        >
                            <span class="block">Everything</span>
                            <span class="block text-gradient">Your Pet</span>
                            <span class="block">Deserves</span>
                        </h1>

                        <!-- Subtitle -->
                        <p
                            x-show="loaded"
                            x-transition:enter="transition ease-out duration-1000 delay-400"
                            x-transition:enter-start="opacity-0 translate-y-6"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="mt-8 text-lg sm:text-xl text-zinc-600 dark:text-zinc-400 max-w-md leading-relaxed"
                        >
                            Curated essentials for the ones who make our lives whole. Quality nutrition, cozy spaces, and playful moments.
                        </p>

                        <!-- CTA Buttons -->
                        <div
                            x-show="loaded"
                            x-transition:enter="transition ease-out duration-1000 delay-600"
                            x-transition:enter-start="opacity-0 translate-y-6"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="mt-10 flex flex-wrap items-center gap-4"
                        >
                            <a
                                href="{{ route('shop.index') }}"
                                wire:navigate
                                class="group inline-flex items-center gap-3 px-8 py-4 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-full font-medium text-sm transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-zinc-900/20 dark:hover:shadow-white/10"
                            >
                                Shop Collection
                                <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                            <a
                                href="{{ route('shop.categories') }}"
                                wire:navigate
                                class="group inline-flex items-center gap-3 px-8 py-4 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white rounded-full font-medium text-sm transition-all duration-300 hover:bg-zinc-50 dark:hover:bg-zinc-800"
                            >
                                Explore Categories
                                <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>

                        <!-- Trust Indicators -->
                        <div
                            x-show="loaded"
                            x-transition:enter="transition ease-out duration-1000 delay-800"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="mt-12 flex items-center gap-6 text-sm text-zinc-500 dark:text-zinc-500"
                        >
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 border-2 border-white dark:border-zinc-900"></div>
                                <div class="w-8 h-8 rounded-full bg-zinc-300 dark:bg-zinc-600 border-2 border-white dark:border-zinc-900"></div>
                                <div class="w-8 h-8 rounded-full bg-zinc-400 dark:bg-zinc-500 border-2 border-white dark:border-zinc-900"></div>
                            </div>
                            <p>Trusted by 10,000+ pet parents</p>
                        </div>
                    </div>

                    <!-- Right Content - Visual Element -->
                    <div
                        x-show="loaded"
                        x-transition:enter="transition ease-out duration-1200 delay-300"
                        x-transition:enter-start="opacity-0 scale-95 translate-x-8"
                        x-transition:enter-end="opacity-100 scale-100 translate-x-0"
                        class="order-1 lg:order-2 relative"
                    >
                        <div class="relative aspect-square max-w-lg mx-auto lg:max-w-none">
                            <!-- Decorative Rings -->
                            <div class="absolute inset-0 rounded-full border border-zinc-200 dark:border-zinc-800 scale-110 animate-pulse-soft"></div>
                            <div class="absolute inset-0 rounded-full border border-zinc-100 dark:border-zinc-900 scale-125 opacity-50"></div>

                            <!-- Main Image Container -->
                            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-zinc-100 to-zinc-50 dark:from-zinc-800 dark:to-zinc-900 aspect-square">
                                <img
                                    src="{{ asset('logo.png') }}"
                                    alt="Petkhazana"
                                    class="w-full h-full object-cover opacity-90 dark:opacity-80"
                                />
                                <!-- Overlay Gradient -->
                                <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/10 to-transparent dark:from-zinc-900/30"></div>
                            </div>

                            <!-- Floating Card -->
                            <div class="absolute -bottom-4 -left-4 lg:-bottom-6 lg:-left-6 bg-white dark:bg-zinc-800 rounded-2xl p-4 shadow-xl border border-zinc-100 dark:border-zinc-700 animate-float">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-zinc-900 dark:bg-white flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white dark:text-zinc-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">Premium Quality</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Vet Approved</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Stats Card -->
                            <div class="absolute -top-4 -right-4 lg:-top-6 lg:-right-6 bg-white dark:bg-zinc-800 rounded-2xl p-4 shadow-xl border border-zinc-100 dark:border-zinc-700 animate-float" style="animation-delay: 1s;">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">500+</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Products</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-container>

        <!-- Scroll Indicator - Desktop Only -->
        <div class="hidden lg:block absolute bottom-8 left-1/2 -translate-x-1/2">
            <div class="w-6 h-10 rounded-full border-2 border-zinc-300 dark:border-zinc-600 flex justify-center pt-2">
                <div class="w-1 h-2 bg-zinc-400 dark:bg-zinc-500 rounded-full animate-bounce"></div>
            </div>
        </div>
    </section>

    <!-- Trust Badges Section -->
    <section class="relative py-12 bg-zinc-50/50 dark:bg-zinc-900/30 border-y border-zinc-100 dark:border-zinc-800">
        <x-container>
            <x-trust-badges />
        </x-container>
    </section>

    <!-- Categories Section - Prominent Image Focus -->
    <livewire:home.shop-by-category />

    <!-- Featured Products Section -->
    <livewire:home.featured-products />

    <!-- Collections Section -->
    <livewire:home.featured-collections />

    <!-- Newsletter / CTA Section -->
    <section class="relative py-24 lg:py-32 overflow-hidden">
        <div class="absolute inset-0 bg-zinc-900 dark:bg-white"></div>
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0); background-size: 32px 32px;"></div>
        </div>

        <x-container class="relative">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white dark:text-zinc-900 font-heading mb-6">
                    Join the Pack
                </h2>
                <p class="text-lg text-zinc-400 dark:text-zinc-600 mb-10">
                    Get exclusive deals, pet care tips, and new arrival updates delivered to your inbox.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                    <input
                        type="email"
                        placeholder="Enter your email"
                        class="flex-1 px-6 py-4 rounded-full bg-white/10 dark:bg-zinc-900/10 border border-white/20 dark:border-zinc-900/20 text-white dark:text-zinc-900 placeholder-zinc-500 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-white/50 dark:focus:ring-zinc-900/50 transition-all"
                    />
                    <button class="px-8 py-4 rounded-full bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white font-medium hover:scale-105 transition-transform duration-300">
                        Subscribe
                    </button>
                </div>
            </div>
        </x-container>
    </section>
</div>
