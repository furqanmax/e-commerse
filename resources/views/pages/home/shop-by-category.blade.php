<div>
    @if ($this->categories->isNotEmpty())
        <section class="relative py-16 sm:py-20 lg:py-28 bg-white dark:bg-[#0a0a0a]">
            <x-container>
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10 lg:mb-14">
                    <div>
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2 block">Discover</span>
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-zinc-900 dark:text-white font-heading tracking-tight">
                            Shop by Category
                        </h2>
                    </div>
                    <a
                        href="{{ route('shop.categories') }}"
                        wire:navigate
                        class="group hidden sm:inline-flex items-center gap-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
                    >
                        View All
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>

                <!-- Mobile: Horizontal Scroll -->
                <div class="lg:hidden -mx-4 px-4">
                    <div class="flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-hide" style="-webkit-overflow-scrolling: touch;">
                        @foreach ($this->categories as $category)
                            <div class="flex-none w-[160px] snap-start">
                                <x-category-card :$category variant="featured" />
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Desktop: Grid Layout -->
                <div class="hidden lg:grid grid-cols-4 gap-6">
                    @foreach ($this->categories as $index => $category)
                        <x-category-card :$category variant="featured" class="delay-{{ ($index % 4) * 100 }}" />
                    @endforeach
                </div>

                <!-- Mobile View All Link -->
                <div class="mt-8 text-center lg:hidden">
                    <a
                        href="{{ route('shop.categories') }}"
                        wire:navigate
                        class="inline-flex items-center gap-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
                    >
                        View All Categories
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </x-container>
        </section>
    @endif
</div>
