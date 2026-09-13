@extends('layouts.base', ['title' => 'Hindi PDF Library — Classic & Contemporary Hindi Literature'])

@section('content')
<div class="space-y-16 pb-16">
    <!-- Section 1: Hero / Discovery Banner -->
    <section class="relative overflow-hidden bg-gradient-to-b from-amber-50/70 via-slate-50 to-slate-50 border-b border-slate-200/80 py-12 sm:py-20">
        <x-ui.container size="default" class="text-center">
            <!-- Trust Badge -->
            <div class="inline-flex items-center gap-2 rounded-full bg-amber-100/80 px-3 py-1 text-xs font-semibold text-brand-900 mb-6 border border-amber-200/80 shadow-2xs">
                <svg class="h-3.5 w-3.5 text-brand-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
                <span>डिजिटल हिन्दी पुस्तकालय धरोहर • Free &amp; Legal Digital Library</span>
            </div>

            <!-- Main Heading -->
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight sm:leading-tight max-w-3xl mx-auto">
                Discover, Read &amp; Study Classic Hindi Literature
            </h1>
            
            <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                हजारों कालजयी उपन्यास, कहानियाँ, काव्य संग्रह और वैचारिक पुस्तकें निःशुल्क पढ़ें और सुरक्षित पीडीएफ डाउनलोड करें।
            </p>

            <!-- Centered Discovery Search Box -->
            <div class="mt-8 max-w-xl mx-auto">
                <x-search-box size="large" action="{{ route('search') }}" placeholder="Search books, authors (मुंशी प्रेमचंद, गोदान, कबीर...)" />
                <div class="mt-2.5 flex flex-wrap items-center justify-center gap-2 text-xs text-slate-500">
                    <span class="font-medium text-slate-400">Popular:</span>
                    <a href="{{ route('search', ['q' => 'गोदान']) }}" class="hover:text-brand-700 underline underline-offset-2">गोदान (Godan)</a>
                    <span>•</span>
                    <a href="{{ route('search', ['q' => 'मधुशाला']) }}" class="hover:text-brand-700 underline underline-offset-2">मधुशाला</a>
                    <span>•</span>
                    <a href="{{ route('search', ['q' => 'प्रेमचंद']) }}" class="hover:text-brand-700 underline underline-offset-2">प्रेमचंद</a>
                    <span>•</span>
                    <a href="{{ route('search', ['q' => 'रश्मिरथी']) }}" class="hover:text-brand-700 underline underline-offset-2">रश्मिरथी</a>
                </div>
            </div>
        </x-ui.container>
    </section>

    <!-- Section 2: Featured Literature -->
    <section id="featured-books">
        <x-ui.container size="default">
            <x-ui.section-header 
                title="Featured Literature (प्रमुख पुस्तकें)"
                subtitle="Curated cultural works, classic fiction, and digital archive collections."
                actionLabel="Explore All Books"
                actionUrl="{{ route('search') }}"
            />

            @if ($featuredBooks->isNotEmpty())
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($featuredBooks as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-200 bg-white p-8 text-center">
                    <p class="text-xs text-slate-500">Curated featured titles are being prepared for public discovery.</p>
                </div>
            @endif
        </x-ui.container>
    </section>

    <!-- Section 3: Popular & Trending Books -->
    @if ($popularBooks->isNotEmpty())
        <section id="popular-books">
            <x-ui.container size="default">
                <x-ui.section-header 
                    title="Popular &amp; Trending (सर्वाधिक लोकप्रिय पुस्तकें)"
                    subtitle="Most read and referenced literary works among our readers."
                    actionLabel="View All"
                    actionUrl="{{ route('search', ['sort' => 'popular']) }}"
                />

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($popularBooks as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>
            </x-ui.container>
        </section>
    @endif

    <!-- Section 4: Top Categories -->
    <section id="browse-categories">
        <x-ui.container size="default">
            <x-ui.section-header 
                title="Explore By Category (श्रेणी अनुसार खोजें)"
                subtitle="Find literature categorized by literary forms, academic subjects, and disciplines."
                actionLabel="Search Library"
                actionUrl="{{ route('search') }}"
            />

            @if ($categories->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($categories as $category)
                        <x-category-card :category="$category" />
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-200 bg-white p-8 text-center">
                    <p class="text-xs text-slate-500">Subject categories are being compiled.</p>
                </div>
            @endif
        </x-ui.container>
    </section>

    <!-- Section 5: Prominent Authors -->
    <section id="browse-authors">
        <x-ui.container size="default">
            <x-ui.section-header 
                title="Distinguished Authors (प्रतिष्ठित लेखक)"
                subtitle="Explore legendary literary figures of Hindi, Sanskrit, and regional literature."
                actionLabel="Browse Authors"
                actionUrl="{{ route('search') }}"
            />

            @if ($authors->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($authors as $author)
                        <x-author-card :author="$author" />
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-200 bg-white p-8 text-center">
                    <p class="text-xs text-slate-500">Author catalog entries are being cataloged.</p>
                </div>
            @endif
        </x-ui.container>
    </section>

    <!-- Section 6: Highly Rated Books -->
    @if ($highlyRatedBooks->isNotEmpty())
        <section id="highly-rated-books">
            <x-ui.container size="default">
                <x-ui.section-header 
                    title="Highly Rated Works (उच्च मूल्यांकित रचनाएँ)"
                    subtitle="Acclaimed masterworks reviewed and revered by scholars and readers."
                    actionLabel="Browse All Rated"
                    actionUrl="{{ route('search', ['sort' => 'rating']) }}"
                />

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($highlyRatedBooks as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>
            </x-ui.container>
        </section>
    @endif

    <!-- Section 7: Recently Added Books -->
    @if ($recentBooks->isNotEmpty())
        <section id="recently-added-books">
            <x-ui.container size="default">
                <x-ui.section-header 
                    title="Recently Added (नवीनतम जोड़ी गई पुस्तकें)"
                    subtitle="New additions to the digital library catalog and cultural archives."
                    actionLabel="View Latest"
                    actionUrl="{{ route('search', ['sort' => 'newest']) }}"
                />

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($recentBooks as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>
            </x-ui.container>
        </section>
    @endif

    <!-- Section 8: Browse by Language -->
    @if ($languages->isNotEmpty())
        <section id="browse-languages">
            <x-ui.container size="default">
                <x-ui.section-header 
                    title="Browse by Language (भाषा अनुसार संग्रह)"
                    subtitle="Explore digitized titles in Hindi, Sanskrit, and regional Indic languages."
                />

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach ($languages as $language)
                        <a href="{{ route('search', ['language' => $language->id]) }}" class="group flex flex-col p-4 rounded-xl border border-slate-200 bg-white shadow-2xs hover:border-brand-500 hover:shadow-xs transition">
                            <span class="text-xs text-slate-400 font-mono uppercase">{{ $language->code }}</span>
                            <span class="text-sm font-bold text-slate-900 group-hover:text-brand-700 transition mt-1">
                                {{ $language->native_name ?? $language->name }}
                            </span>
                            <span class="text-[11px] text-slate-500 mt-0.5">
                                {{ $language->name }} • {{ $language->books_count }} {{ $language->books_count === 1 ? 'Book' : 'Books' }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </x-ui.container>
        </section>
    @endif

    <!-- Section 9: Cultural Trust & Mission Callout -->
    <section id="cultural-trust">
        <x-ui.container size="default">
            <div class="rounded-2xl border border-amber-200/80 bg-gradient-to-r from-amber-50/80 via-white to-amber-50/50 p-6 sm:p-10 shadow-xs">
                <div class="max-w-3xl">
                    <div class="flex items-center gap-2 text-brand-800 font-semibold text-xs tracking-wider uppercase mb-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                        Public Domain &amp; Open Knowledge Commitment
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900">
                        Preserving India's Written Cultural Heritage in the Digital Age
                    </h2>
                    <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                        Hindi PDF Library is dedicated to cataloging and providing free, accessible digital editions of public-domain cultural treasures. Every title is audited for copyright status to ensure open, safe educational exploration for students, scholars, and readers across the globe.
                    </p>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <x-ui.button href="{{ route('search') }}" variant="primary" size="sm">
                            Explore Public Library
                        </x-ui.button>
                        <a href="{{ route('home') }}#cultural-trust" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                            Rights &amp; Public Domain Policy
                        </a>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>
</div>
@endsection
