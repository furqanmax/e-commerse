<div>
    @if ($this->collections->isNotEmpty())
        <section class="relative py-16 sm:py-20 lg:py-28 bg-white dark:bg-[#0a0a0a]">
            <x-container>
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10 lg:mb-14">
                    <div>
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-2 block">Curated</span>
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-zinc-900 dark:text-white font-heading tracking-tight">
                            Shop Collections
                        </h2>
                    </div>
                </div>

                <!-- Collections Grid - Bento Style -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                    @foreach($this->collections as $index => $collection)
                        @if ($index === 0)
                            <!-- First Collection - Large -->
                            <div class="md:col-span-2 lg:col-span-2 lg:row-span-2">
                                <x-collection-banner :$collection size="large" />
                            </div>
                        @elseif ($index === 1)
                            <!-- Second Collection -->
                            <div class="lg:row-span-1">
                                <x-collection-banner :$collection size="medium" />
                            </div>
                        @else
                            <!-- Third Collection -->
                            <div class="lg:row-span-1">
                                <x-collection-banner :$collection size="medium" />
                            </div>
                        @endif
                    @endforeach
                </div>
            </x-container>
        </section>
    @endif
</div>
