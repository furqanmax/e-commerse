<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">
    <title>@yield('title', 'PetKhazana')</title>
    <meta name="description" content="@yield('meta_description', 'Your one-stop shop for all pet supplies. Quality products for your beloved pets.')">
    <link rel="canonical" href="{{ url()->current() }}">
    
    @include('partials.head')

    <!-- Fonts/CSS from existing theme -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col font-sans antialiased min-h-screen bg-zinc-50 dark:bg-zinc-900">
    <!-- Header -->
    <livewire:store-header />

    <!-- Main Content -->
    <main class="flex-1 pb-16 lg:pb-0">
        @yield('content')
    </main>

    <!-- Footer -->
    <livewire:store-footer />

    <x-notification />
    @fluxScripts
</body>
</html>
