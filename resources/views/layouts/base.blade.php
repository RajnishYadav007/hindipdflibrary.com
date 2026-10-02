<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#fcfaf7]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Dynamic Semantic SEO, Meta, Canonical, Hreflang & Schema.org Structured Data -->
    <x-seo-head :seo="$seo ?? null" :title="$title ?? null" />

    <!-- Preconnect & Typography Fonts: Inter (Latin) + Noto Sans Devanagari (Hindi) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|noto-sans-devanagari:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite Pipeline -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex flex-col min-h-screen font-sans text-slate-800 bg-[#fcfaf7] antialiased selection:bg-amber-100 selection:text-amber-900 pb-16 md:pb-0">
    <!-- Public Site Header -->
    <x-header />

    <!-- Main Content Area -->
    <main id="main-content" class="flex-1">
        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    <!-- Public Site Footer -->
    <x-footer />

    <!-- Mobile Bottom Navigation (Visible on mobile screens) -->
    <x-mobile-bottom-nav />

    <!-- Global Audio TTS Player (Excluded on Book pages where audio is strictly forbidden) -->
    @unless(request()->routeIs('books.*', 'book.*', 'books.show', 'books.read', 'books.download', 'books.interactive_reader', 'books.plural.*'))
        <x-audio-player />
    @endunless
    <x-book-drawer />

    @livewireScripts
</body>
</html>
