<header class="sticky top-0 z-40 border-b border-warm-200/80 bg-white/95 backdrop-blur-md transition-colors" x-data="{ mobileMenuOpen: false }">
    <!-- Top Bar -->
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-2.5 sm:px-6 lg:px-8">
        <!-- Brand Identity -->
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group focus-visible:outline-terracotta-600 rounded-md">
                <!-- Warm Terracotta Rounded Book Icon -->
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-terracotta-600 text-white shadow-sm group-hover:bg-terracotta-700 transition">
                    <svg class="h-5 w-5 fill-none stroke-current stroke-2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z" />
                        <path d="M6 6h10" />
                        <path d="M6 10h10" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-base sm:text-lg font-bold tracking-tight text-ink-900 group-hover:text-terracotta-600 transition leading-tight font-serif">
                        {{ config('app.name', 'हिंदी PDF लाइब्रेरी') }}
                    </span>
                    <span class="text-[11px] font-medium text-slate-500 tracking-wide">
                        पढ़ें • सुनें • सीखें • साझा करें
                    </span>
                </div>
            </a>
        </div>

        <!-- Center Prominent Search (Desktop) -->
        <div class="hidden md:flex flex-1 max-w-xl mx-4">
            <form action="{{ route('search') }}" method="GET" class="relative w-full">
                <div class="relative flex items-center w-full">
                    <div class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input 
                        type="search" 
                        name="q" 
                        placeholder="किताबें, लेखक, विषय या ISBN से खोजें..." 
                        class="block w-full rounded-full border border-warm-300 bg-warm-50/50 py-2.5 pl-10 pr-12 text-xs sm:text-sm text-ink-900 placeholder-slate-400 transition focus:border-terracotta-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-terracotta-500/20"
                        autocomplete="off"
                    />
                    <button 
                        type="submit" 
                        aria-label="Search"
                        class="absolute right-1.5 top-1/2 -translate-y-1/2 flex h-8 w-8 items-center justify-center rounded-full bg-terracotta-600 text-white hover:bg-terracotta-700 transition shadow-xs cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Controls: Language, Theme & Auth -->
        <div class="hidden md:flex items-center gap-3 shrink-0">
            <!-- Language Switcher -->
            <x-language-switcher />


            <!-- Authentication Buttons -->
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-slate-700 hover:text-terracotta-600 px-3 py-1.5 rounded-full hover:bg-warm-100 transition">
                        Dashboard
                    </a>
                    @if (auth()->user()->is_admin || auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor', 'Moderator']))
                        <a href="{{ url('/admin') }}" class="inline-flex items-center gap-1 rounded-full bg-ink-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-ink-800 transition shadow-xs">
                            <svg class="h-3.5 w-3.5 text-terracotta-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                            </svg>
                            Admin
                        </a>
                    @endif
                @else
                    <!-- Login Button (Outline with User Icon) -->
                    <a 
                        href="{{ route('login') }}" 
                        class="inline-flex items-center gap-1.5 rounded-full border border-warm-300 bg-white px-3.5 py-1.5 text-xs font-bold text-slate-700 hover:bg-warm-50 hover:text-terracotta-600 transition shadow-2xs"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span>लॉगिन</span>
                    </a>

                    <!-- Register Button (Solid Terracotta) -->
                    @if (Route::has('register'))
                        <a 
                            href="{{ route('register') }}" 
                            class="inline-flex items-center justify-center rounded-full bg-terracotta-600 px-4 py-1.5 text-xs font-bold text-white hover:bg-terracotta-700 transition shadow-xs hover:scale-102"
                        >
                            रजिस्टर
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Mobile Controls (Search trigger, Theme, Hamburger) -->
        <div class="flex items-center gap-1.5 md:hidden">
            <button 
                type="button" 
                @click="document.getElementById('mobile-search-bar')?.classList.toggle('hidden')"
                class="inline-flex items-center justify-center rounded-lg p-2 text-slate-700 hover:bg-warm-100 transition"
                aria-label="Search"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </button>

            <button 
                type="button" 
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="inline-flex items-center justify-center rounded-lg p-2 text-slate-700 hover:bg-warm-100 transition focus-visible:outline-terracotta-600"
                aria-label="Toggle menu"
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

    <!-- Desktop Sub-Navigation Bar -->
    <nav aria-label="Main Navigation" class="hidden md:block border-t border-warm-200/60 bg-white">
        <div class="mx-auto flex max-w-7xl items-center gap-8 px-4 sm:px-6 lg:px-8 text-xs font-semibold">
            <a 
                href="{{ route('home') }}" 
                class="py-2.5 border-b-2 transition flex items-center gap-1 {{ request()->routeIs('home', 'home.root') ? 'border-terracotta-600 text-terracotta-600 font-bold' : 'border-transparent text-slate-600 hover:text-ink-900 hover:border-slate-300' }}"
            >
                होम
            </a>
            <a 
                href="{{ route('books.index') }}" 
                class="py-2.5 border-b-2 transition {{ request()->routeIs('books.index') ? 'border-terracotta-600 text-terracotta-600 font-bold' : 'border-transparent text-slate-600 hover:text-ink-900 hover:border-slate-300' }}"
            >
                पुस्तकें
            </a>
            <a 
                href="{{ route('audiobooks.index') }}" 
                class="py-2.5 border-b-2 transition {{ request()->routeIs('audiobooks.*') ? 'border-terracotta-600 text-terracotta-600 font-bold' : 'border-transparent text-slate-600 hover:text-ink-900 hover:border-slate-300' }}"
            >
                ऑडियो बुक्स
            </a>
            <a 
                href="{{ route('chalisa.index') }}" 
                class="py-2.5 border-b-2 transition {{ request()->routeIs('chalisa.*', 'aarti.*', 'devotional.*') ? 'border-terracotta-600 text-terracotta-600 font-bold' : 'border-transparent text-slate-600 hover:text-ink-900 hover:border-slate-300' }}"
            >
                चालीसा व आरती
            </a>
            <a 
                href="{{ route('stories.index') }}" 
                class="py-2.5 border-b-2 transition {{ request()->routeIs('stories.*') ? 'border-terracotta-600 text-terracotta-600 font-bold' : 'border-transparent text-slate-600 hover:text-ink-900 hover:border-slate-300' }}"
            >
                बाल कहानियाँ
            </a>
            <a 
                href="{{ route('authors.index') }}" 
                class="py-2.5 border-b-2 transition {{ request()->routeIs('authors.*') ? 'border-terracotta-600 text-terracotta-600 font-bold' : 'border-transparent text-slate-600 hover:text-ink-900 hover:border-slate-300' }}"
            >
                लेखक
            </a>
            <a 
                href="{{ route('categories.index') }}" 
                class="py-2.5 border-b-2 transition {{ request()->routeIs('categories.*') ? 'border-terracotta-600 text-terracotta-600 font-bold' : 'border-transparent text-slate-600 hover:text-ink-900 hover:border-slate-300' }}"
            >
                श्रेणियाँ
            </a>
            <a 
                href="{{ route('pages.about') }}" 
                class="py-2.5 border-b-2 transition {{ request()->routeIs('pages.about') ? 'border-terracotta-600 text-terracotta-600 font-bold' : 'border-transparent text-slate-600 hover:text-ink-900 hover:border-slate-300' }}"
            >
                हमारे बारे में
            </a>
        </div>
    </nav>

    <!-- Mobile Search Bar (Expandable on Mobile) -->
    <div id="mobile-search-bar" class="hidden border-t border-warm-200 px-4 py-2.5 md:hidden bg-warm-50/70">
        <form action="{{ route('search') }}" method="GET" class="relative w-full">
            <input 
                type="search" 
                name="q" 
                placeholder="किताबें, लेखक, विषय या ISBN से खोजें..." 
                class="block w-full rounded-full border border-warm-300 bg-white py-2 pl-9 pr-10 text-xs text-ink-900 placeholder-slate-400 focus:border-terracotta-500 focus:ring-1 focus:ring-terracotta-500"
            />
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 rounded-full bg-terracotta-600 p-1.5 text-white">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </button>
        </form>
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
        class="border-t border-warm-200 bg-white px-4 py-4 md:hidden shadow-lg"
        style="display: none;"
    >
        <nav class="flex flex-col space-y-1 text-sm font-medium text-slate-700">
            <a href="{{ route('home') }}" class="px-3 py-2 rounded-xl hover:bg-warm-100 transition {{ request()->routeIs('home', 'home.root') ? 'font-bold text-terracotta-600 bg-warm-100' : '' }}">
                होम (Home)
            </a>
            <a href="{{ route('books.index') }}" class="px-3 py-2 rounded-xl hover:bg-warm-100 transition {{ request()->routeIs('books.*') ? 'font-bold text-terracotta-600 bg-warm-100' : '' }}">
                पुस्तकें (Books)
            </a>
            <a href="{{ route('audiobooks.index') }}" @click="mobileMenuOpen = false" class="px-3 py-2 rounded-xl hover:bg-warm-100 transition {{ request()->routeIs('audiobooks.*') ? 'font-bold text-terracotta-600 bg-warm-100' : '' }}">
                ऑडियो बुक्स (Audiobooks)
            </a>
            <a href="{{ route('chalisa.index') }}" @click="mobileMenuOpen = false" class="px-3 py-2 rounded-xl hover:bg-warm-100 transition {{ request()->routeIs('chalisa.*', 'aarti.*') ? 'font-bold text-terracotta-600 bg-warm-100' : '' }}">
                चालीसा व आरती (Chalisa &amp; Aarti)
            </a>
            <a href="{{ route('stories.index') }}" @click="mobileMenuOpen = false" class="px-3 py-2 rounded-xl hover:bg-warm-100 transition {{ request()->routeIs('stories.*') ? 'font-bold text-terracotta-600 bg-warm-100' : '' }}">
                बाल कहानियाँ (Children's Stories)
            </a>
            <a href="{{ route('authors.index') }}" class="px-3 py-2 rounded-xl hover:bg-warm-100 transition {{ request()->routeIs('authors.*') ? 'font-bold text-terracotta-600 bg-warm-100' : '' }}">
                लेखक (Authors)
            </a>
            <a href="{{ route('categories.index') }}" class="px-3 py-2 rounded-xl hover:bg-warm-100 transition {{ request()->routeIs('categories.*') ? 'font-bold text-terracotta-600 bg-warm-100' : '' }}">
                श्रेणियाँ (Categories)
            </a>
            <a href="{{ route('pages.about') }}" class="px-3 py-2 rounded-xl hover:bg-warm-100 transition">
                हमारे बारे में (About Us)
            </a>

            <!-- Mobile Auth & Language Row -->
            <div class="border-t border-warm-200 pt-3 mt-2 flex flex-col gap-2">
                <div class="flex items-center justify-between px-3 py-1">
                    <span class="text-xs text-slate-500 font-medium">भाषा / Language:</span>
                    <x-language-switcher />
                </div>

                @auth
                    <a href="{{ route('dashboard') }}" class="w-full text-center rounded-xl bg-warm-100 py-2.5 text-xs font-bold text-slate-800">
                        User Dashboard
                    </a>
                @else
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <a href="{{ route('login') }}" class="text-center rounded-full border border-warm-300 py-2 text-xs font-bold text-slate-700 hover:bg-warm-50">
                            लॉगिन
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-center rounded-full bg-terracotta-600 py-2 text-xs font-bold text-white hover:bg-terracotta-700">
                                रजिस्टर
                            </a>
                        @endif
                    </div>
                @endauth
            </div>
        </nav>
    </div>
</header>