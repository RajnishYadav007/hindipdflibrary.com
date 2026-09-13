<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Hindi PDF Library') }}</title>

    <!-- Preconnect & Typography Fonts: Inter (Latin) + Noto Sans Devanagari (Hindi) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|noto-sans-devanagari:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite Pipeline -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex flex-col min-h-screen font-sans text-slate-800 bg-slate-50 antialiased selection:bg-brand-100 selection:text-brand-900">
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

    @livewireScripts
</body>
</html>
