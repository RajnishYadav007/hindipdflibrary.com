<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-charcoal-900 bg-[#fcfbf7] selection:bg-terracotta-100 selection:text-terracotta-900">
        <div class="min-h-screen pb-16 md:pb-0">
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white border-b border-warm-200 shadow-2xs">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Global Audio TTS Player (Excluded on Book pages where audio is strictly forbidden) -->
        @unless(request()->routeIs('books.*', 'book.*', 'books.show', 'books.read', 'books.download', 'books.interactive_reader', 'books.plural.*'))
            <x-audio-player />
        @endunless
        <x-book-drawer />

        <!-- Mobile Bottom Tab Navigation -->
        <x-mobile-bottom-nav />
    </body>
</html>
