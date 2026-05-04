<div>
    <x-container class="py-8 sm:py-12">
        <!-- Header -->
        <!-- <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <flux:heading size="xl">{{ __('Shop') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Browse our entire collection') }}</flux:text>
            </div>
            <flux:select wire:model.live="sort" class="w-auto">
                <flux:select.option value="latest">{{ __('Newest') }}</flux:select.option>
                <flux:select.option value="name">{{ __('Name') }}</flux:select.option>
            </flux:select>
        </div> -->

        <!-- Categories Section -->
        <div class="mb-8">
            <!-- Section Header -->
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Categories') }}</h2>
                <a
                    href="{{ route('shop.categories') }}"
                    wire:navigate
                    class="text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300"
                >
                    {{ __('See All') }}
                </a>
            </div>

            <!-- Categories Horizontal Scroll -->
            <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide" style="-webkit-overflow-scrolling: touch;">
                <!-- All Category -->
                <button
                    type="button"
                    wire:click="$set('category', null)"
                    class="flex flex-col items-center flex-shrink-0 group"
                >
                    <div @class([
                        'relative size-16 lg:size-20 rounded-full overflow-hidden transition-all duration-300 flex items-center justify-center',
                        'bg-emerald-100  ring-emerald-500 ' => !$category,
                        'bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700' => $category,
                    ])>
                        <flux:icon.squares-2x2 class="size-8 text-zinc-500 dark:text-zinc-400" />
                    </div>
                    <span @class([
                        'mt-2 text-xs font-medium text-center',
                        'text-emerald-600 dark:text-emerald-400' => !$category,
                        'text-zinc-600 dark:text-zinc-400' => $category,
                    ])>
                        {{ __('All') }}
                    </span>
                </button>

                <!-- Category Items -->
                @foreach($this->categories as $cat)
                    @php
                        $thumbnailCollection = config('shopper.media.storage.thumbnail_collection');
                        $image = $cat->getFirstMediaUrl($thumbnailCollection);
                        $fallback = shopper_fallback_url();
                        $isActive = $category === $cat->id;
                    @endphp
                    <button
                        type="button"
                        wire:click="$set('category', {{ $cat->id }})"
                        class="flex flex-col items-center flex-shrink-0 group"
                    >
                        <div @class([
                            'relative size-16 lg:size-20 rounded-full overflow-hidden transition-all duration-300',
                            'ring-2 ring-emerald-500 ring-offset-2' => $isActive,
                            'group-hover:ring-2 group-hover:ring-zinc-300 dark:group-hover:ring-zinc-600' => !$isActive,
                        ])>
                            <img
                                src="{{ $image ?: $fallback }}"
                                alt="{{ $cat->name }}"
                                class="size-full object-cover object-center"
                                loading="lazy"
                            />
                        </div>
                        <span @class([
                            'mt-2 text-xs font-medium text-center transition-colors',
                            'text-emerald-600 dark:text-emerald-400' => $isActive,
                            'text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-zinc-200' => !$isActive,
                        ])>
                            {{ $cat->name }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Search and Filters Section -->
        <div class="mb-8">
            <div class="flex items-center gap-3">
                <!-- Search Bar -->
                <div class="flex-1">
                    <flux:input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search products...') }}"
                        icon="magnifying-glass"
                        class="w-full"
                    />
                </div>

                <!-- Filter Button -->
                <flux:modal.trigger name="filter-modal">
                    <flux:button variant="subtle" icon="funnel" class="shrink-0" />
                </flux:modal.trigger>
            </div>
        </div>

        <!-- Filter Modal -->
        <flux:modal name="filter-modal" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Filters') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Refine your search') }}</flux:text>
                </div>

                <!-- Sort -->
                <div>
                    <flux:label>{{ __('Sort by') }}</flux:label>
                    <flux:select wire:model.live="sort" class="mt-2 w-full">
                        <flux:select.option value="latest">{{ __('Newest') }}</flux:select.option>
                        <flux:select.option value="name">{{ __('Name') }}</flux:select.option>
                    </flux:select>
                </div>

                <!-- Active Category Display -->
                @if($category)
                    <div>
                        <flux:label>{{ __('Category') }}</flux:label>
                        <div class="mt-2 flex items-center gap-2">
                            <flux:badge variant="primary">
                                {{ $this->categories->firstWhere('id', $category)?->name }}
                            </flux:badge>
                            <button
                                type="button"
                                wire:click="$set('category', null)"
                                class="text-sm text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                            >
                                {{ __('Clear') }}
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Clear All -->
                @if($category || $search)
                    <flux:separator />
                    <div class="flex gap-3">
                        <flux:button
                            variant="primary"
                            wire:click="$set('search', ''); $set('category', null);"
                            class="w-full"
                        >
                            {{ __('Clear All Filters') }}
                        </flux:button>
                    </div>
                @endif
            </div>
        </flux:modal>

        <!-- Products Grid -->
        <div>
            @if($this->products->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <flux:icon.magnifying-glass variant="outline" class="size-12 text-zinc-300 dark:text-zinc-600" />
                    <flux:heading size="sm" class="mt-4">{{ __('No products found') }}</flux:heading>
                    <flux:text size="sm" class="mt-1">{{ __('Try adjusting your search or filters.') }}</flux:text>
                </div>
            @else
                <div class="grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-6">
                    @foreach($this->products as $product)
                        <x-product-card :$product />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8 flex justify-center">
                    {{ $this->products->links() }}
                </div>
            @endif
        </div>
    </x-container>
</div>