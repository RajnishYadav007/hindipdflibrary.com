@extends('layouts.base')

@php
    $translation = $book->translations->where('locale', 'hi')->first() 
        ?? $book->translations->where('locale', 'en')->first() 
        ?? $book->translations->first();
    
    $title = $translation?->title ?? $book->slug;
    $primaryAuthor = $book->authors->first();
    $authorName = $primaryAuthor?->translations->where('locale', 'hi')->first()?->name 
        ?? $primaryAuthor?->translations->where('locale', 'en')->first()?->name 
        ?? $primaryAuthor?->slug;

    $primaryCategory = $book->categories->first();
    $categoryName = $primaryCategory?->translations->where('locale', 'hi')->first()?->name 
        ?? $primaryCategory?->translations->where('locale', 'en')->first()?->name 
        ?? $primaryCategory?->slug;

    $coverMedia = $book->media->where('collection_name', 'cover')->first() ?? $book->media->first();
    $coverUrl = $coverMedia ? asset('storage/' . $coverMedia->file_name) : null;

    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
    ];
    if ($primaryCategory) {
        $breadcrumbs[] = ['label' => $categoryName, 'url' => route('categories.show', $primaryCategory->slug)];
    }
    $breadcrumbs[] = ['label' => $title, 'url' => null];

    $keyThemes = is_array($translation?->key_themes) 
        ? $translation->key_themes 
        : (is_string($translation?->key_themes) ? json_decode($translation->key_themes, true) : []);
@endphp

@section('title', $title . ($authorName ? ' — ' . $authorName : '') . ' | Hindi PDF Library')

@section('content')
<div class="space-y-12 pb-16">
    <!-- Breadcrumb -->
    <div class="border-b border-slate-200/80 bg-slate-50/50 py-3">
        <x-ui.container size="default">
            <x-breadcrumb :items="$breadcrumbs" />
        </x-ui.container>
    </div>

    <!-- Book Hero / Main Info Section -->
    <section>
        <x-ui.container size="default">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12">
                <!-- Left: Book Cover & Quick CTAs -->
                <div class="md:col-span-4 lg:col-span-4 flex flex-col items-center">
                    <div class="w-full max-w-xs sticky top-24 space-y-4">
                        <!-- Cover Container -->
                        <div class="relative aspect-[2/3] w-full overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-amber-900 to-amber-950 p-1 shadow-book">
                            @if ($coverUrl)
                                <img 
                                    src="{{ $coverUrl }}" 
                                    alt="Cover of {{ $title }}" 
                                    class="h-full w-full rounded-xl object-cover"
                                    loading="eager"
                                />
                            @else
                                <div class="flex h-full w-full flex-col justify-between rounded-xl bg-gradient-to-br from-amber-800 to-amber-950 p-6 text-center text-amber-50">
                                    <div class="flex items-center justify-between text-xs tracking-widest uppercase opacity-70">
                                        <span>{{ $book->originalLanguage?->code ?? 'HI' }}</span>
                                        <span>DIGITAL EDITION</span>
                                    </div>
                                    <div class="my-auto">
                                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-white/10">
                                            <svg class="h-6 w-6 text-amber-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                            </svg>
                                        </div>
                                        <h2 class="font-serif text-lg font-bold leading-snug text-amber-100">
                                            {{ $title }}
                                        </h2>
                                        @if ($authorName)
                                            <p class="mt-2 text-xs text-amber-200/90 font-medium">
                                                {{ $authorName }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-[10px] tracking-wider opacity-60 uppercase">
                                        Hindi PDF Library
                                    </div>
                                </div>
                            @endif

                            <div class="absolute top-3 right-3 z-10">
                                <x-favorite-button :isFavorited="false" size="default" />
                            </div>
                        </div>

                        <!-- Truthful Call-To-Action Area -->
                        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs space-y-3 text-center">
                            @if ($pdfMedia)
                                <a 
                                    href="{{ asset('storage/' . $pdfMedia->file_name) }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-brand-800 transition focus-visible:outline-2 focus-visible:outline-brand-700"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    Read / Download PDF
                                </a>
                                <p class="text-[11px] text-slate-500">
                                    Format: PDF • {{ $pdfMedia->size ? round($pdfMedia->size / 1048576, 1) . ' MB' : 'Digital Document' }}
                                </p>
                            @elseif ($book->source_url)
                                <a 
                                    href="{{ $book->source_url }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer nofollow"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-amber-800 transition focus-visible:outline-2 focus-visible:outline-amber-700"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                    Access at Primary Source
                                </a>
                                <p class="text-[11px] text-slate-500">
                                    Curated Archive Link • External Digital Archive
                                </p>
                            @else
                                <div class="rounded-lg bg-slate-50 border border-slate-200 p-3">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                        <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                        </svg>
                                        Reference Catalog Edition
                                    </span>
                                    <p class="mt-1 text-[11px] text-slate-500">
                                        Bibliographic archive record. Digitized copy currently undergoing verification.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Metadata, Overview, Themes, and Details -->
                <div class="md:col-span-8 lg:col-span-8 space-y-8">
                    <!-- Title & Core Meta -->
                    <div>
                        @if ($primaryCategory)
                            <a href="{{ route('categories.show', $primaryCategory->slug) }}" class="inline-flex items-center rounded-md bg-amber-100/80 px-2.5 py-0.5 text-xs font-semibold text-brand-800 hover:bg-amber-200 transition">
                                {{ $categoryName }}
                            </a>
                        @endif

                        <h1 class="mt-2 text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            {{ $title }}
                        </h1>

                        @if ($book->authors->isNotEmpty())
                            <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-600">
                                <span>By</span>
                                @foreach ($book->authors as $index => $author)
                                    @php
                                        $currName = $author->translations->where('locale', 'hi')->first()?->name 
                                            ?? $author->translations->where('locale', 'en')->first()?->name 
                                            ?? $author->slug;
                                    @endphp
                                    <a href="{{ route('authors.show', $author->slug) }}" class="font-semibold text-brand-700 hover:text-brand-900 hover:underline">
                                        {{ $currName }}
                                    </a>{{ $loop->last ? '' : ',' }}
                                @endforeach
                            </div>
                        @endif

                        <!-- Ratings & Quick Stats Bar -->
                        <div class="mt-4 flex flex-wrap items-center gap-4 border-y border-slate-200/80 py-3 text-xs text-slate-600">
                            <div class="flex items-center gap-1.5">
                                <x-rating-stars :rating="$book->rating_average" :ratingCount="$book->rating_count" size="sm" />
                            </div>

                            <div class="h-3 w-px bg-slate-200"></div>

                            <div>
                                <span class="font-semibold text-slate-900">{{ number_format($book->view_count) }}</span> Views
                            </div>

                            @if ($book->publication_year)
                                <div class="h-3 w-px bg-slate-200"></div>
                                <div>
                                    Published: <span class="font-semibold text-slate-900">{{ $book->publication_year }}</span>
                                </div>
                            @endif

                            @if ($book->pages)
                                <div class="h-3 w-px bg-slate-200"></div>
                                <div>
                                    <span class="font-semibold text-slate-900">{{ $book->pages }}</span> Pages
                                </div>
                            @endif

                            @if ($book->originalLanguage)
                                <div class="h-3 w-px bg-slate-200"></div>
                                <div>
                                    Language: <span class="font-semibold text-slate-900 uppercase">{{ $book->originalLanguage->code }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Key Themes / Badges -->
                    @if (!empty($keyThemes))
                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                                Key Themes &amp; Subjects (प्रमुख विषय)
                            </h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($keyThemes as $theme)
                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 border border-slate-200/60">
                                        #{{ is_array($theme) ? ($theme['name'] ?? json_encode($theme)) : $theme }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- About / Description -->
                    @if ($translation?->description || $translation?->short_description)
                        <div class="prose prose-slate max-w-none space-y-3">
                            <h2 class="text-lg font-bold text-slate-900">
                                About the Book (पुस्तक परिचय)
                            </h2>
                            @if ($translation?->short_description)
                                <p class="text-sm sm:text-base font-medium text-slate-800 leading-relaxed italic border-l-2 border-brand-500 pl-3">
                                    {{ $translation->short_description }}
                                </p>
                            @endif
                            @if ($translation?->description)
                                <div class="text-sm sm:text-base leading-relaxed text-slate-700 space-y-3">
                                    {!! nl2br(e($translation->description)) !!}
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Summary / Synopsis -->
                    @if ($translation?->summary)
                        <div class="rounded-xl border border-amber-200/60 bg-amber-50/40 p-5">
                            <h2 class="text-sm font-bold text-amber-950 uppercase tracking-wider mb-2">
                                Literary Summary &amp; Synopsis (कथानक व सारांश)
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                {!! nl2br(e($translation->summary)) !!}
                            </p>
                        </div>
                    @endif

                    <!-- Why Read This Book -->
                    @if ($translation?->why_read)
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-2xs">
                            <div class="flex items-center gap-2 text-brand-800 text-sm font-bold mb-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                                Why You Should Read This Book (यह पुस्तक क्यों पढ़ें?)
                            </div>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                {!! nl2br(e($translation->why_read)) !!}
                            </p>
                        </div>
                    @endif

                    <!-- Detailed Technical & Bibliographic Metadata Table -->
                    <div>
                        <h2 class="text-base font-bold text-slate-900 mb-3">
                            Bibliographic Information (ग्रंथ सूची विवरण)
                        </h2>
                        <div class="rounded-xl border border-slate-200 bg-white shadow-2xs overflow-hidden">
                            <dl class="divide-y divide-slate-100 text-xs">
                                <div class="grid grid-cols-3 p-3 sm:px-4 bg-slate-50/50">
                                    <dt class="font-medium text-slate-500">Book Title</dt>
                                    <dd class="col-span-2 font-semibold text-slate-900">{{ $title }}</dd>
                                </div>
                                @if ($authorName)
                                    <div class="grid grid-cols-3 p-3 sm:px-4">
                                        <dt class="font-medium text-slate-500">Author</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $authorName }}</dd>
                                    </div>
                                @endif
                                @if ($book->publisher)
                                    <div class="grid grid-cols-3 p-3 sm:px-4 bg-slate-50/50">
                                        <dt class="font-medium text-slate-500">Publisher</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $book->publisher }}</dd>
                                    </div>
                                @endif
                                @if ($book->publication_year)
                                    <div class="grid grid-cols-3 p-3 sm:px-4">
                                        <dt class="font-medium text-slate-500">Publication Year</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $book->publication_year }}</dd>
                                    </div>
                                @endif
                                @if ($book->isbn)
                                    <div class="grid grid-cols-3 p-3 sm:px-4 bg-slate-50/50">
                                        <dt class="font-medium text-slate-500">ISBN</dt>
                                        <dd class="col-span-2 font-mono text-slate-800">{{ $book->isbn }}</dd>
                                    </div>
                                @endif
                                @if ($book->isbn13)
                                    <div class="grid grid-cols-3 p-3 sm:px-4">
                                        <dt class="font-medium text-slate-500">ISBN-13</dt>
                                        <dd class="col-span-2 font-mono text-slate-800">{{ $book->isbn13 }}</dd>
                                    </div>
                                @endif
                                @if ($book->edition)
                                    <div class="grid grid-cols-3 p-3 sm:px-4 bg-slate-50/50">
                                        <dt class="font-medium text-slate-500">Edition</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $book->edition }}</dd>
                                    </div>
                                @endif
                                @if ($book->pages)
                                    <div class="grid grid-cols-3 p-3 sm:px-4">
                                        <dt class="font-medium text-slate-500">Total Pages</dt>
                                        <dd class="col-span-2 text-slate-800">{{ $book->pages }}</dd>
                                    </div>
                                @endif
                                @if ($book->license_type || $book->copyright_status)
                                    <div class="grid grid-cols-3 p-3 sm:px-4 bg-slate-50/50">
                                        <dt class="font-medium text-slate-500">Copyright / License</dt>
                                        <dd class="col-span-2 text-slate-800">
                                            {{ $book->license_type ?? 'Public Domain Heritage' }} 
                                            @if ($book->copyright_status)
                                                ({{ $book->copyright_status }})
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>

    <!-- Author Biography Card (if author present) -->
    @if ($primaryAuthor)
        @php
            $authorTrans = $primaryAuthor->translations->where('locale', 'hi')->first() 
                ?? $primaryAuthor->translations->where('locale', 'en')->first() 
                ?? $primaryAuthor->translations->first();
        @endphp
        @if ($authorTrans?->biography)
            <section class="border-t border-slate-200/80 pt-8">
                <x-ui.container size="default">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-2xs">
                        <div class="flex flex-col sm:flex-row items-start gap-5">
                            <div class="h-16 w-16 shrink-0 rounded-full bg-amber-100 border border-amber-200 text-brand-800 flex items-center justify-center font-bold text-xl">
                                {{ mb_substr($authorName ?? 'A', 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-3">
                                    <h3 class="text-base font-bold text-slate-900">
                                        About Author: {{ $authorName }}
                                    </h3>
                                    <a href="{{ route('authors.show', $primaryAuthor->slug) }}" class="text-xs font-semibold text-brand-700 hover:text-brand-900">
                                        View Author Profile &rarr;
                                    </a>
                                </div>
                                <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                                    {{ $authorTrans->biography }}
                                </p>
                            </div>
                        </div>
                    </div>
                </x-ui.container>
            </section>
        @endif
    @endif

    <!-- Community Ratings & Reviews Section -->
    <section id="reviews-section" class="border-t border-slate-200/80 pt-10">
        <x-ui.container size="default">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">
                        Reader Reviews &amp; Ratings (पाठक समीक्षा)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Moderated feedback and literary reflections from our community.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <x-rating-stars :rating="$book->rating_average" :ratingCount="$book->rating_count" size="md" />
                </div>
            </div>

            @if ($reviews->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($reviews as $review)
                        <x-review-card :review="$review" />
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/50 p-8 text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800">No public reviews yet</h3>
                    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                        Be the first reader to share critical thoughts on this work. Reader submission workflow will open shortly.
                    </p>
                </div>
            @endif
        </x-ui.container>
    </section>

    <!-- More by this Author -->
    @if ($authorBooks->isNotEmpty())
        <section class="border-t border-slate-200/80 pt-10">
            <x-ui.container size="default">
                <x-ui.section-header 
                    title="More by this Author (लेखक की अन्य रचनाएँ)"
                    subtitle="Explore other literary masterworks by {{ $authorName }}."
                    actionLabel="View Author Catalog"
                    actionUrl="{{ $primaryAuthor ? route('authors.show', $primaryAuthor->slug) : '#' }}"
                />

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($authorBooks as $authorBook)
                        <x-book-card :book="$authorBook" />
                    @endforeach
                </div>
            </x-ui.container>
        </section>
    @endif

    <!-- Related Books in Category -->
    @if ($relatedBooks->isNotEmpty())
        <section class="border-t border-slate-200/80 pt-10">
            <x-ui.container size="default">
                <x-ui.section-header 
                    title="Related Books (समान विषय की पुस्तकें)"
                    subtitle="Discover similar literature in {{ $categoryName }}."
                    actionLabel="View Category"
                    actionUrl="{{ $primaryCategory ? route('categories.show', $primaryCategory->slug) : '#' }}"
                />

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($relatedBooks as $relBook)
                        <x-book-card :book="$relBook" />
                    @endforeach
                </div>
            </x-ui.container>
        </section>
    @endif

    <!-- Frequently Asked Questions (FAQ) Section -->
    <section class="border-t border-slate-200/80 pt-10">
        <x-ui.container size="default">
            <div class="max-w-3xl">
                <h2 class="text-xl font-bold text-slate-900 mb-4">
                    Frequently Asked Questions (अक्सर पूछे जाने वाले प्रश्न)
                </h2>

                <div class="space-y-3" x-data="{ active: null }">
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                        <button 
                            @click="active = (active === 1 ? null : 1)" 
                            class="flex w-full items-center justify-between text-left text-sm font-semibold text-slate-900"
                        >
                            <span>Is this edition of {{ $title }} free to read and download?</span>
                            <span class="text-slate-400" x-text="active === 1 ? '−' : '+'"></span>
                        </button>
                        <div x-show="active === 1" x-collapse class="mt-2 text-xs text-slate-600 leading-relaxed">
                            Yes. The Hindi PDF Library provides open, free access to public-domain cultural works and verified open-access materials for academic, cultural, and personal reading.
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                        <button 
                            @click="active = (active === 2 ? null : 2)" 
                            class="flex w-full items-center justify-between text-left text-sm font-semibold text-slate-900"
                        >
                            <span>What formats are supported for reading?</span>
                            <span class="text-slate-400" x-text="active === 2 ? '−' : '+'"></span>
                        </button>
                        <div x-show="active === 2" x-collapse class="mt-2 text-xs text-slate-600 leading-relaxed">
                            Digitized documents are provided in standard PDF format suitable for desktop screens, mobile devices, tablets, and e-readers.
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
                        <button 
                            @click="active = (active === 3 ? null : 3)" 
                            class="flex w-full items-center justify-between text-left text-sm font-semibold text-slate-900"
                        >
                            <span>How can I contribute corrections or report bibliographic errors?</span>
                            <span class="text-slate-400" x-text="active === 3 ? '−' : '+'"></span>
                        </button>
                        <div x-show="active === 3" x-collapse class="mt-2 text-xs text-slate-600 leading-relaxed">
                            Our digital archives are maintained by researchers and community moderators. You can reach out through our contact and policy channels to suggest metadata corrections.
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>
</div>
@endsection
