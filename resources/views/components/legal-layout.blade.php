@props([
    'title' => null,
])

<div class="container mx-auto px-4 py-8 max-w-4xl">
    @if($title)
        <div class="mb-8 text-center">
            {{ $title }}
        </div>
    @endif
    
    <div class="prose prose-lg max-w-none dark:prose-invert">
        {{ $slot }}
    </div>
</div>