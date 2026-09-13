<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur-xs shadow-2xs" x-data="{ mobileMenuOpen: false }">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <!-- Brand & Library Identity -->
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group focus-visible:outline-brand-700 rounded-md">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-700 text-white shadow-xs group-hover:bg-brand-800 transition">
                    <x-application-logo class="h-5 w-5" />
                </div>
                <div class="flex flex-col">
                    <span class="text-base font-bold tracking-tight text-slate-900 group-hover:text-brand-800 transition leading-tight">
                        {{ config('app.name', 'Hindi PDF Library') }}
                    </span>
                    <span class="text-[11px] font-medium text-slate-500 tracking-wide">
                        डिजिटल पुस्तक पुस्तकालय
                    </span>
                </div>
            </a>
        </div>

        <!-- Center Search (Desktop) -->
        <div class="hidden md:flex flex-1 max-w-md mx-4">
            <x-search-box />
        </div>

        <!-- Desktop Navigation & Controls -->
        <div class="hidden md:flex items-center gap-3 shrink-0">
            <!-- Nav Links -->
            <nav class="flex items-center gap-5 mr-2 text-sm font-medium text-slate-600">
                <a href="{{ route('home') }}" class="hover:text-slate-900 transition {{ request()->routeIs('home') ? 'text-brand-800 font-semibold' : '' }}">
                    Home
                </a>
                <a href="#browse-books" class="hover:text-slate-900 transition">
                    Books
                </a>
                <a href="#browse-authors" class="hover:text-slate-900 transition">
                    Authors
                </a>
                <a href="#browse-categories" class="hover:text-slate-900 transition">
                    Categories
                </a>
            </nav>

            <div class="h-4 w-px bg-slate-200" aria-hidden="true"></div>

            <!-- Language Switcher -->
            <x-language-switcher />

            <!-- User Authentication Area -->
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900 px-2 py-1 rounded transition">
                        Dashboard
                    </a>
                    @if (auth()->user()->is_admin || auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor', 'Moderator']))
                        <a href="{{ url('/admin') }}" class="inline-flex items-center gap-1 rounded-md bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800 transition shadow-2xs">
                            <svg class="h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                            </svg>
                            Admin
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900 px-2 py-1 transition">
                        Sign In
                    </a>
                    @if (Route::has('register'))
                        <x-ui.button :href="route('register')" variant="primary" size="sm">
                            Join Library
                        </x-ui.button>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Mobile Controls (Hamburger & Language) -->
        <div class="flex items-center gap-2 md:hidden">
            <x-language-switcher />

            <button 
                type="button" 
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="inline-flex items-center justify-center rounded-lg p-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition focus-visible:outline-brand-700"
                aria-label="Toggle navigation menu"
                :aria-expanded="mobileMenuOpen"
            >
                <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Search Bar (Directly below header on small screens) -->
    <div class="border-t border-slate-100 px-4 py-2.5 md:hidden bg-slate-50/70">
        <x-search-box />
    </div>

    <!-- Mobile Drawer Menu -->
    <div 
        x-show="mobileMenuOpen" 
        x-transition:enter="transition ease-out duration-150" 
        x-transition:enter-start="opacity-0 -translate-y-2" 
        x-transition:enter-end="opacity-100 translate-y-0" 
        x-transition:leave="transition ease-in duration-100" 
        x-transition:leave-start="opacity-100 translate-y-0" 
        x-transition:leave-end="opacity-0 -translate-y-2" 
        class="border-t border-slate-200 bg-white px-4 py-4 md:hidden shadow-lg"
        style="display: none;"
    >
        <nav class="flex flex-col space-y-3 text-sm font-medium">
            <a href="{{ route('home') }}" class="px-2 py-1.5 text-slate-800 hover:bg-slate-50 rounded-md transition {{ request()->routeIs('home') ? 'font-semibold text-brand-800' : '' }}">
                Home
            </a>
            <a href="#browse-books" class="px-2 py-1.5 text-slate-800 hover:bg-slate-50 rounded-md transition">
                Books (पुस्तकें)
            </a>
            <a href="#browse-authors" class="px-2 py-1.5 text-slate-800 hover:bg-slate-50 rounded-md transition">
                Authors (लेखक)
            </a>
            <a href="#browse-categories" class="px-2 py-1.5 text-slate-800 hover:bg-slate-50 rounded-md transition">
                Categories (श्रेणियाँ)
            </a>

            <div class="border-t border-slate-100 pt-3 flex flex-col space-y-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-2 py-1.5 text-slate-800 hover:bg-slate-50 rounded-md transition">
                        User Dashboard
                    </a>
                    @if (auth()->user()->is_admin || auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor', 'Moderator']))
                        <a href="{{ url('/admin') }}" class="px-2 py-1.5 font-semibold text-brand-800 hover:bg-brand-50 rounded-md transition">
                            Admin Portal
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="w-full text-center rounded-md border border-slate-300 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Sign In
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="w-full text-center rounded-md bg-brand-700 py-2 text-sm font-semibold text-white hover:bg-brand-800 transition shadow-xs">
                            Join Library
                        </a>
                    @endif
                @endauth
            </div>
        </nav>
    </div>
</header>
