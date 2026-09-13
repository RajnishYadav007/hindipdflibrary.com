<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Hindi PDF Library') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex flex-col min-h-screen text-slate-800 antialiased">
    <!-- Site Header -->
    <header class="border-b border-slate-200 bg-white shadow-xs">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="text-xl font-bold tracking-tight text-slate-900 hover:text-amber-600 transition">
                    {{ config('app.name', 'Hindi PDF Library') }}
                </a>
                <span class="rounded bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">
                    Foundation
                </span>
            </div>

            <!-- Header Navigation -->
            <nav class="flex items-center space-x-4 text-sm font-medium">
                <a href="{{ route('home') }}" class="text-slate-600 hover:text-slate-900 transition">
                    Home
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-slate-900 transition">
                        Dashboard
                    </a>
                    @if (auth()->user()->is_admin)
                        <a href="{{ url('/admin') }}" class="rounded bg-slate-900 px-3 py-1.5 text-white hover:bg-slate-800 transition">
                            Admin Portal
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-900 transition">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded bg-amber-600 px-3 py-1.5 text-white hover:bg-amber-700 transition">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        @if (isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    <!-- Site Footer -->
    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-6 text-center text-xs text-slate-500 sm:px-6 lg:px-8">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Hindi PDF Library') }}. All rights reserved.</p>
            <p class="mt-1">Digital book & literature discovery platform foundation.</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
