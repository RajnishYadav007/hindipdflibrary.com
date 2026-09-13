@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'खोजें' : 'Search Books', 'url' => null],
    ];
@endphp

@section('content')
<div class="space-y-8 pb-16">
    <!-- Breadcrumb -->
    <div class="border-b border-slate-200/80 bg-slate-50/50 py-3">
        <x-ui.container size="default">
            <x-breadcrumb :items="$breadcrumbs" />
        </x-ui.container>
    </div>

    <section>
        <x-ui.container size="default">
            <!-- Search & Filters Control Panel -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs">
                <form action="{{ route('search') }}" method="GET" class="space-y-4">
                    <!-- Keyword Search Field -->
                    <div class="max-w-3xl">
                        <label for="search-input" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            {{ $locale === 'hi' ? 'खोज प्रश्न' : 'Search Query' }}
                        </label>
                        <div class="relative flex items-center">
                            <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="search" 
                                name="q" 
                                id="search-input" 
                                value="{{ $query }}" 
                                placeholder="{{ $locale === 'hi' ? 'शीर्षक, लेखक, श्रेणी या ISBN से खोजें...' : 'Search by title, author, category, or ISBN...' }}"
                                class="block w-full rounded-xl border border-slate-300 py-2.5 pl-11 pr-24 text-sm text-slate-900 placeholder-slate-400 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20"
                            />
                            <button 
                                type="submit" 
                                class="absolute right-1.5 rounded-lg bg-brand-700 px-4 py-1.5 text-xs font-semibold text-white hover:bg-brand-800 transition"
                            >
                                {{ $locale === 'hi' ? 'खोजें' : 'Search' }}
                            </button>
                        </div>
                    </div>

                    <!-- Faceted Filter Bar -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-2 border-t border-slate-100">
                        <!-- Category Filter -->
                        <div>
                            <label for="filter-category" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                {{ $locale === 'hi' ? 'श्रेणी' : 'Category' }}
                            </label>
                            <select 
                                name="category" 
                                id="filter-category" 
                                onchange="this.form.submit()" 
                                class="block w-full rounded-lg border border-slate-300 bg-white py-1.5 px-3 text-xs text-slate-700 focus:border-brand-600 focus:outline-none"
                            >
                                <option value="">{{ $locale === 'hi' ? 'सभी श्रेणियाँ' : 'All Categories' }}</option>
                                @foreach ($categories as $cat)
                                    @php
                                        $catName = $cat->localized($locale)?->name ?? $cat->slug;
                                    @endphp
                                    <option value="{{ $cat->id }}" @selected($categoryId == $cat->id)>
                                        {{ $catName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Genre Filter -->
                        <div>
                            <label for="filter-genre" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                {{ $locale === 'hi' ? 'विधा / जॉनर' : 'Genre' }}
                            </label>
                            <select 
                                name="genre" 
                                id="filter-genre" 
                                onchange="this.form.submit()" 
                                class="block w-full rounded-lg border border-slate-300 bg-white py-1.5 px-3 text-xs text-slate-700 focus:border-brand-600 focus:outline-none"
                            >
                                <option value="">{{ $locale === 'hi' ? 'सभी विधाएँ' : 'All Genres' }}</option>
                                @foreach ($genres as $genre)
                                    @php
                                        $genName = $genre->localized($locale)?->name ?? $genre->slug;
                                    @endphp
                                    <option value="{{ $genre->id }}" @selected($genreId == $genre->id)>
                                        {{ $genName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Author Filter -->
                        <div>
                            <label for="filter-author" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                {{ $locale === 'hi' ? 'लेखक' : 'Author' }}
                            </label>
                            <select 
                                name="author" 
                                id="filter-author" 
                                onchange="this.form.submit()" 
                                class="block w-full rounded-lg border border-slate-300 bg-white py-1.5 px-3 text-xs text-slate-700 focus:border-brand-600 focus:outline-none"
                            >
                                <option value="">{{ $locale === 'hi' ? 'सभी लेखक' : 'All Authors' }}</option>
                                @foreach ($authors as $author)
                                    @php
                                        $authorName = $author->localized($locale)?->name ?? $author->slug;
                                    @endphp
                                    <option value="{{ $author->id }}" @selected($authorId == $author->id)>
                                        {{ $authorName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Language Filter -->
                        <div>
                            <label for="filter-language" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                {{ $locale === 'hi' ? 'भाषा' : 'Language' }}
                            </label>
                            <select 
                                name="language" 
                                id="filter-language" 
                                onchange="this.form.submit()" 
                                class="block w-full rounded-lg border border-slate-300 bg-white py-1.5 px-3 text-xs text-slate-700 focus:border-brand-600 focus:outline-none"
                            >
                                <option value="">{{ $locale === 'hi' ? 'सभी भाषाएँ' : 'All Languages' }}</option>
                                @foreach ($languages as $lang)
                                    <option value="{{ $lang->id }}" @selected($languageId == $lang->id)>
                                        {{ $lang->native_name ?? $lang->name }} ({{ strtoupper($lang->code) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort Order -->
                        <div>
                            <label for="filter-sort" class="block text-[11px] font-semibold text-slate-500 mb-1">
                                {{ $locale === 'hi' ? 'क्रम' : 'Sort By' }}
                            </label>
                            <select 
                                name="sort" 
                                id="filter-sort" 
                                onchange="this.form.submit()" 
                                class="block w-full rounded-lg border border-slate-300 bg-white py-1.5 px-3 text-xs text-slate-700 focus:border-brand-600 focus:outline-none"
                            >
                                <option value="newest" @selected($sort === 'newest')>{{ $locale === 'hi' ? 'नवीनतम' : 'Newest Added' }}</option>
                                <option value="popular" @selected($sort === 'popular')>{{ $locale === 'hi' ? 'सर्वाधिक लोकप्रिय' : 'Most Viewed / Popular' }}</option>
                                <option value="rating" @selected($sort === 'rating')>{{ $locale === 'hi' ? 'सर्वोत्तम रेटिंग' : 'Highest Rated' }}</option>
                                <option value="title" @selected($sort === 'title')>{{ $locale === 'hi' ? 'शीर्षक अनुसार (A–Z)' : 'Title (A–Z)' }}</option>
                                <option value="oldest" @selected($sort === 'oldest')>{{ $locale === 'hi' ? 'पुरातन' : 'Oldest Added' }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Reset Filters Button if Active -->
                    @if ($query !== '' || $categoryId || $genreId || $authorId || $languageId || $sort !== 'newest')
                        <div class="flex items-center gap-2 pt-2">
                            <a href="{{ route('search') }}" class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-800 font-medium">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                {{ $locale === 'hi' ? 'सभी फ़िल्टर और खोज साफ़ करें' : 'Clear all filters & search' }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </x-ui.container>
    </section>

    <!-- Search Results Grid Section -->
    <section>
        <x-ui.container size="default">
            <!-- Results Heading & Counter -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">
                        @if ($query !== '')
                            {{ $locale === 'hi' ? 'खोज परिणाम:' : 'Search Results for' }} <span class="text-brand-700">"{{ $query }}"</span>
                        @else
                            {{ $locale === 'hi' ? 'पुस्तकालय कैटलॉग खोज' : 'Library Catalog Discovery' }}
                        @endif
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $locale === 'hi' ? "कुल {$books->total()} प्रकाशित पुस्तकें मिलीं" : "Found {$books->total()} published " . ($books->total() === 1 ? 'book' : 'books') }}
                    </p>
                </div>
            </div>

            @if ($books->isNotEmpty())
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($books as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $books->links() }}
                </div>
            @else
                <!-- Empty Search Results State -->
                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/60 p-12 text-center max-w-2xl mx-auto">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-brand-800 mb-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h2 class="text-base font-bold text-slate-900">
                        {{ $locale === 'hi' ? 'कोई पुस्तक नहीं मिली' : 'No books found' }}
                    </h2>
                    <p class="mt-1.5 text-xs text-slate-500 leading-relaxed max-w-md mx-auto">
                        {{ $locale === 'hi' 
                            ? 'आपकी खोज के अनुसार कोई प्रकाशित पुस्तक नहीं मिली। कृपया वर्तनी जांचें या फ़िल्टर बदलकर पुनः प्रयास करें।'
                            : "We couldn't find any published titles matching your search criteria. Try checking your spelling or adjusting your filters." }}
                    </p>
                    <div class="mt-5 flex justify-center gap-3">
                        <a href="{{ route('search') }}" class="rounded-lg border border-slate-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                            {{ $locale === 'hi' ? 'फ़िल्टर रीसेट करें' : 'Reset Search Filters' }}
                        </a>
                        <a href="{{ route('home') }}" class="rounded-lg bg-brand-700 px-3.5 py-1.5 text-xs font-semibold text-white shadow-xs hover:bg-brand-800 transition">
                            {{ $locale === 'hi' ? 'मुख्य पृष्ठ पर जाएँ' : 'Back to Homepage' }}
                        </a>
                    </div>
                </div>

                <!-- Discovery Recommendations for Zero Results -->
                @if ($suggestedBooks->isNotEmpty())
                    <div class="mt-14 pt-8 border-t border-slate-200">
                        <div class="mb-6">
                            <h2 class="text-lg font-bold text-slate-900">
                                {{ $locale === 'hi' ? 'लोकप्रिय पुस्तकें जो आपको पसंद आ सकती हैं' : 'Popular Books You Might Like' }}
                            </h2>
                            <p class="text-xs text-slate-500">
                                {{ $locale === 'hi' ? 'हमारे पाठकों द्वारा सबसे अधिक पढ़ी जाने वाली पुस्तकें' : 'Most read and explored titles by our readers' }}
                            </p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4">
                            @foreach ($suggestedBooks as $suggestedBook)
                                <x-book-card :book="$suggestedBook" />
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </x-ui.container>
    </section>
</div>
@endsection
