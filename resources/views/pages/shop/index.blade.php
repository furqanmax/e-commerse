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
            <!-- <flux:heading size="lg" class="mb-4">{{ __('Categories') }}</flux:heading> -->
            <div class="flex flex-wrap gap-2 sm:gap-3">
                <button
                    type="button"
                    wire:click="$set('category', null)"
                    @class([
                        'px-4 py-2 rounded-full text-sm font-medium transition-all duration-200',
                        'bg-blue-600 text-white dark:bg-blue-500' => !$category,
                        'bg-zinc-200 text-zinc-700 hover:bg-zinc-300 dark:bg-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-600' => $category,
                    ])
                >
                    {{ __('All') }}
                </button>
                @foreach($this->categories as $cat)
                    <button
                        type="button"
                        wire:click="$set('category', {{ $cat->id }})"
                        @class([
                            'px-4 py-2 rounded-full text-sm font-medium transition-all duration-200',
                            'bg-blue-600 text-white dark:bg-blue-500' => $category === $cat->id,
                            'bg-zinc-200 text-zinc-700 hover:bg-zinc-300 dark:bg-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-600' => $category !== $cat->id,
                        ])
                    >
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Search and Filters Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row gap-4">
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
                
                <!-- Sort Dropdown (Mobile) -->
                <div class="sm:hidden">
                    <flux:select wire:model.live="sort" class="w-full">
                        <flux:select.option value="latest">{{ __('Newest') }}</flux:select.option>
                        <flux:select.option value="name">{{ __('Name') }}</flux:select.option>
                    </flux:select>
                </div>
            </div>
        </div>

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