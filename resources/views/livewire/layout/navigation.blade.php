<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-warm-300 sticky top-0 z-30">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-6">
                <!-- Brand & Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-ink-900 text-white shadow-xs group-hover:bg-ink-800 transition">
                            <x-application-logo class="h-5 w-5" />
                        </div>
                        <div class="hidden sm:flex flex-col">
                            <span class="text-sm font-bold tracking-tight text-ink-900 group-hover:text-terracotta-600 transition leading-tight font-serif">
                                {{ config('app.name', 'Hindi PDF Library') }}
                            </span>
                            <span class="text-[10px] font-medium text-slate-400">
                                पाठक पोर्टल (Reader Dashboard)
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:flex items-center">
                    <a 
                        href="{{ route('dashboard') }}" 
                        class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold leading-5 transition {{ request()->routeIs('dashboard') && !request()->has('tab') ? 'border-ink-900 text-ink-900 font-bold' : 'border-transparent text-slate-500 hover:text-ink-900 hover:border-warm-300' }}"
                    >
                        {{ __('Dashboard') }}
                    </a>

                    <a 
                        href="{{ route('dashboard', ['tab' => 'submissions']) }}" 
                        class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition {{ request('tab') === 'submissions' ? 'border-ink-900 text-ink-900 font-bold' : 'border-transparent text-slate-500 hover:text-ink-900 hover:border-warm-300' }}"
                    >
                        मेरी प्रस्तुतियां (Submissions)
                    </a>

                    <!-- Universal Contribute Dropdown Menu -->
                    <div class="relative flex items-center" x-data="{ openContribute: false }">
                        <button 
                            type="button" 
                            @click="openContribute = !openContribute"
                            @click.away="openContribute = false"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white shadow-xs transition cursor-pointer"
                        >
                            <span>+ नया साहित्य जोड़ें</span>
                            <svg class="w-3.5 h-3.5 transition-transform" :class="{'rotate-180': openContribute}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div 
                            x-show="openContribute" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            style="display: none;" 
                            class="absolute left-0 top-full mt-1.5 w-60 rounded-2xl bg-white p-2 shadow-xl border border-slate-200 z-50 text-xs"
                        >
                            <a href="{{ route('dashboard.books.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="book-open" class="h-4 w-4 text-amber-700 shrink-0" />
                                <span class="font-semibold">नई पुस्तक (PDF Book)</span>
                            </a>
                            <a href="{{ route('dashboard.submissions.audiobook.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="headphones" class="h-4 w-4 text-amber-700 shrink-0" />
                                <span class="font-semibold">ऑडियोबुक (Audiobook)</span>
                            </a>
                            <a href="{{ route('dashboard.submissions.chalisa.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="flame" class="h-4 w-4 text-amber-700 shrink-0" />
                                <span class="font-semibold">श्री चालीसा (Chalisa)</span>
                            </a>
                            <a href="{{ route('dashboard.submissions.aarti.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="sparkles" class="h-4 w-4 text-amber-700 shrink-0" />
                                <span class="font-semibold">पावन आरती (Aarti)</span>
                            </a>
                            <a href="{{ route('dashboard.submissions.story.create', ['type' => 'children']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="sparkles" class="h-4 w-4 text-emerald-600 shrink-0" />
                                <span class="font-semibold">बाल कथा (Children Story)</span>
                            </a>
                            <a href="{{ route('dashboard.submissions.story.create', ['type' => 'general']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="scroll-text" class="h-4 w-4 text-amber-700 shrink-0" />
                                <span class="font-semibold">अन्य / लोक कथा (General Story)</span>
                            </a>
                        </div>
                    </div>

                    <a 
                        href="{{ route('books.index') }}" 
                        class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate-500 hover:text-ink-900 hover:border-warm-300 transition"
                    >
                        पुस्तकालय संग्रह (Catalog)
                    </a>
                </div>
            </div>

            <!-- Settings & Profile Area -->
            <div class="hidden sm:flex sm:items-center sm:gap-3">
                @if (auth()->user()->is_admin || auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor', 'Moderator']))
                    <a 
                        href="{{ url('/admin') }}" 
                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800 transition shadow-2xs"
                    >
                        <span>Admin</span>
                    </a>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-warm-300 text-xs font-bold rounded-xl text-ink-900 bg-white hover:bg-warm-50 focus:outline-none transition shadow-2xs">
                            <div class="h-6 w-6 rounded-full bg-warm-200 text-ink-900 flex items-center justify-center font-bold text-xs font-serif">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                            <svg class="fill-current h-3.5 w-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-slate-100 text-xs text-slate-500">
                            <span class="font-bold text-slate-900 block truncate">{{ auth()->user()->name }}</span>
                            <span class="truncate block">{{ auth()->user()->email }}</span>
                        </div>

                        <x-dropdown-link :href="route('dashboard')">
                            {{ __('Dashboard') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('dashboard.books.create')">
                            + नई पुस्तक जोड़ें (Add Book)
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile Settings') }}
                        </x-dropdown-link>

                        <div class="border-t border-slate-100"></div>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link class="text-red-600 hover:text-red-700">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Button for Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-200 bg-white">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') && !request()->has('tab')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard', ['tab' => 'submissions'])" :active="request('tab') === 'submissions'">
                मेरी प्रस्तुतियां (Submissions)
            </x-responsive-nav-link>

            <!-- Quick Add Options -->
            <div class="px-4 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                + नया साहित्य जोड़ें (Contribute)
            </div>
            <x-responsive-nav-link :href="route('dashboard.books.create')" class="pl-6 text-xs flex items-center gap-2">
                <x-app-icon name="book-open" class="h-4 w-4 text-amber-700 shrink-0" />
                <span>नई पुस्तक (PDF Book)</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard.submissions.audiobook.create')" class="pl-6 text-xs flex items-center gap-2">
                <x-app-icon name="headphones" class="h-4 w-4 text-amber-700 shrink-0" />
                <span>ऑडियोबुक (Audiobook)</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard.submissions.chalisa.create')" class="pl-6 text-xs flex items-center gap-2">
                <x-app-icon name="flame" class="h-4 w-4 text-amber-700 shrink-0" />
                <span>श्री चालीसा (Chalisa)</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard.submissions.aarti.create')" class="pl-6 text-xs flex items-center gap-2">
                <x-app-icon name="sparkles" class="h-4 w-4 text-amber-700 shrink-0" />
                <span>पावन आरती (Aarti)</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard.submissions.story.create', ['type' => 'children'])" class="pl-6 text-xs flex items-center gap-2">
                <x-app-icon name="sparkles" class="h-4 w-4 text-emerald-600 shrink-0" />
                <span>बाल कथा (Children Story)</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard.submissions.story.create', ['type' => 'general'])" class="pl-6 text-xs flex items-center gap-2">
                <x-app-icon name="scroll-text" class="h-4 w-4 text-amber-700 shrink-0" />
                <span>अन्य / लोक कथा (General Story)</span>
            </x-responsive-nav-link>

            <div class="border-t border-slate-100 my-1"></div>

            <x-responsive-nav-link :href="route('books.index')">
                पुस्तकालय संग्रह (Browse Catalog)
            </x-responsive-nav-link>
            @if (auth()->user()->is_admin || auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Editor', 'Moderator']))
                <x-responsive-nav-link :href="url('/admin')">
                    Admin Portal
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-slate-100 bg-slate-50 px-4">
            <div class="font-bold text-sm text-slate-800">{{ auth()->user()->name }}</div>
            <div class="font-medium text-xs text-slate-500">{{ auth()->user()->email }}</div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile Settings') }}
                </x-responsive-nav-link>

                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link class="text-red-600">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
