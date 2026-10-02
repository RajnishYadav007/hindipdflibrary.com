@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $translation = $book->localized($locale);
    $title = $translation?->title ?? $book->slug;

    $primaryAuthor = $book->authors->first();
    $authorName = $primaryAuthor?->localized($locale)?->name ?? $primaryAuthor?->slug;

    $primaryCategory = $book->categories->first();
    $categoryName = $primaryCategory?->localized($locale)?->name ?? $primaryCategory?->slug;

    $coverUrl = $book->cover_url;

    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
    ];
    if ($primaryCategory) {
        $breadcrumbs[] = ['label' => $categoryName, 'url' => route('categories.show', ['slug' => $primaryCategory->slug])];
    }
    $breadcrumbs[] = ['label' => $title, 'url' => null];

    $keyThemes = is_array($translation?->key_themes) 
        ? $translation->key_themes 
        : (is_string($translation?->key_themes) ? json_decode($translation->key_themes, true) : []);

    $bookDescription = $translation?->description 
        ?? ($translation?->summary 
        ?? ($book->translations->first()?->description 
        ?? ($book->translations->first()?->summary 
        ?? ($title . ' — ' . ($authorName ? $authorName . ' द्वारा रचित एक महत्वपूर्ण पुस्तक।' : 'हिन्दी पीडीएफ पुस्तकालय पर अध्ययन हेतु उपलब्ध।')))));

    $hindiTranslation = $book->translations->where('locale', 'hi')->first();
    // Devotional lyrics / recitation text
    $bookLyrics = $translation?->lyrics 
        ?? ($hindiTranslation?->lyrics 
        ?? $book->translations->firstWhere(fn ($t) => !empty($t->lyrics))?->lyrics);
@endphp

@section('content')
<div class="space-y-12 pb-16">
    <!-- Breadcrumb -->
    <div class="border-b border-slate-200/80 bg-slate-50/50 py-3">
        <x-ui.container size="default">
            <x-breadcrumb :items="$breadcrumbs" />
        </x-ui.container>
    </div>

    <!-- Book Hero / Main Info Section (3-column Editorial Layout inspired by Reference 2) -->
    <section>
        <x-ui.container size="default">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
                <!-- Column 1 (Left, 3.5 cols): Large Book Cover & Direct Actions -->
                <div class="lg:col-span-4 flex flex-col items-center">
                    <div class="w-full max-w-xs sticky top-24 space-y-4">
                        <!-- Cover Container -->
                        <div class="relative aspect-[2/3] w-full overflow-hidden rounded-2xl border border-warm-300 bg-white p-1.5 shadow-xl">
                            @if ($coverUrl)
                                <img 
                                    src="{{ $coverUrl }}" 
                                    alt="{{ $title }} - {{ $authorName ? $authorName . ' | ' : '' }}हिन्दी पुस्तक मुखपृष्ठ (Hindi Book Cover)" 
                                    title="{{ $title }} - {{ $authorName ? $authorName . ' | ' : '' }}हिन्दी पीडीएफ पुस्तक"
                                    class="h-full w-full rounded-xl object-cover"
                                    loading="eager"
                                />
                            @else
                                <div class="flex h-full w-full flex-col justify-between rounded-xl bg-gradient-to-br from-ink-950 via-slate-900 to-ink-900 p-6 text-center text-warm-100 border border-ink-800">
                                    <div class="flex items-center justify-between text-[10px] tracking-widest uppercase opacity-70">
                                        <span>{{ $book->originalLanguage?->code ?? 'HI' }}</span>
                                        <span>DIGITAL EDITION</span>
                                    </div>
                                    <div class="my-auto">
                                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-amber-200">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                            </svg>
                                        </div>
                                        <h2 class="font-serif text-lg font-bold leading-snug text-white">
                                            {{ $title }}
                                        </h2>
                                        @if ($authorName)
                                            <p class="mt-2 text-xs text-warm-300 font-medium">
                                                {{ $authorName }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-[10px] tracking-wider opacity-60 uppercase">
                                        Hindi PDF Library
                                    </div>
                                </div>
                            @endif

                            <!-- Favorite Button -->
                            <div class="absolute top-3.5 right-3.5 z-10">
                                <x-favorite-button :isFavorited="false" size="default" />
                            </div>
                        </div>

                        <!-- Legal Badge Indicator -->
                        <div class="rounded-xl border border-warm-300 bg-white p-3 text-center shadow-2xs">
                            @if ($book->canDownloadPdf() && $pdfMedia)
                                <div class="flex items-center justify-center gap-1.5 text-xs font-semibold text-emerald-700">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Verified Public Domain Edition</span>
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    Format: PDF • {{ $pdfMedia->file_name }} ({{ $pdfMedia->size ? round($pdfMedia->size / 1048576, 1) . ' MB' : 'Authorized Edition' }})
                                </p>
                            @elseif ($book->getSafeSourceUrl())
                                <div class="flex items-center justify-center gap-1.5 text-xs font-semibold text-ink-900">
                                    <svg class="h-4 w-4 text-terracotta-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                    <span>Primary Archive Referral</span>
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    Digitized edition hosted at verified institutional repository.
                                </p>
                            @else
                                <div class="flex items-center justify-center gap-1.5 text-xs font-semibold text-slate-700">
                                    <svg class="h-4 w-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                    </svg>
                                    <span>Reference Catalog Edition</span>
                                </div>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    Bibliographic archive record. Digitized copy currently undergoing verification.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Column 2 (Center, 5 cols): Editorial Info, Primary Read Action & Overview -->
                <div class="lg:col-span-5 space-y-6">
                    <div>
                        @if ($primaryCategory)
                            <a href="{{ route('categories.show', $primaryCategory->slug) }}" class="inline-flex items-center rounded-lg bg-warm-200/90 px-2.5 py-0.5 text-xs font-semibold text-ink-900 hover:bg-warm-300 transition">
                                {{ $categoryName }}
                            </a>
                        @endif

                        <h1 class="mt-2 text-2xl sm:text-4xl font-extrabold text-ink-900 tracking-tight leading-tight font-serif">
                            {{ $title }}
                        </h1>

                        @if ($book->authors->isNotEmpty())
                            <div class="mt-2.5 flex flex-wrap items-center gap-1.5 text-sm text-slate-600">
                                <span>By</span>
                                @foreach ($book->authors as $index => $author)
                                    @php
                                        $currName = $author->translations->where('locale', 'hi')->first()?->name 
                                            ?? $author->translations->where('locale', 'en')->first()?->name 
                                            ?? $author->slug;
                                    @endphp
                                    <a href="{{ route('authors.show', $author->slug) }}" class="font-bold text-ink-900 hover:text-terracotta-600 hover:underline">
                                        {{ $currName }}
                                    </a>{{ $loop->last ? '' : ',' }}
                                @endforeach
                            </div>
                        @endif

                        @if ($book->user)
                            <div class="mt-2">
                                <x-contributor-badge :user="$book->user" role="योगदानकर्ता" />
                            </div>
                        @endif

                        <!-- Ratings & Quick Stats -->
                        <div class="mt-3.5 flex flex-wrap items-center gap-3 text-xs text-slate-600 pb-4 border-b border-warm-200">
                            <div class="flex items-center gap-1.5">
                                <x-rating-stars :rating="$book->rating_average" :ratingCount="$book->rating_count" size="sm" />
                            </div>

                            <span class="text-slate-300">•</span>
                            <div>
                                <span class="font-bold text-ink-900">{{ number_format($book->view_count) }}</span> reads
                            </div>

                            @if ($book->publication_year)
                                <span class="text-slate-300">•</span>
                                <div>
                                    <span class="font-bold text-ink-900">{{ $book->publication_year }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- PRIMARY READ ACTION BAR (Most visually prominent action) -->
                    <div class="rounded-2xl border border-warm-300 bg-white p-4 shadow-sm space-y-3">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                            <!-- Primary Read Online Action -->
                            <a 
                                href="{{ route('books.interactive_reader', ['slug' => $book->slug]) }}" 
                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-ink-900 px-5 py-3 text-sm font-bold text-white shadow-md hover:bg-ink-800 transition active:scale-98"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                                <span>Read Online (ऑनलाइन पढ़ें)</span>
                            </a>

                            {{-- Secondary Action: PDF or Official Source --}}
                            @if ($book->canDownloadPdf() && $pdfMedia)
                                <a 
                                    href="{{ route('books.download', ['slug' => $book->slug]) }}" 
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-warm-300 bg-warm-50 hover:bg-warm-100 px-4 py-3 text-xs font-bold text-ink-900 transition shadow-2xs"
                                    title="Read / Download PDF"
                                >
                                    <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    <span>Read / Download PDF</span>
                                </a>
                            @elseif ($book->getSafeSourceUrl())
                                <a 
                                    href="{{ $book->getSafeSourceUrl() }}" 
                                    target="_blank" 
                                    rel="noopener noreferrer nofollow"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-warm-300 bg-warm-50 hover:bg-warm-100 px-4 py-3 text-xs font-bold text-ink-900 transition shadow-2xs"
                                >
                                    <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                    <span>Access at Primary Source</span>
                                </a>
                            @endif

                        </div>
                    </div>

                    <!-- Key Themes / Badges -->
                    @if (!empty($keyThemes))
                        <div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($keyThemes as $theme)
                                    <span class="inline-flex items-center rounded-lg bg-warm-100 px-2.5 py-1 text-xs font-medium text-slate-700 border border-warm-200">
                                        #{{ is_array($theme) ? ($theme['name'] ?? json_encode($theme)) : $theme }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- About / Description -->
                    @if ($translation?->description || $translation?->short_description)
                        <div class="space-y-3">
                            <h2 class="text-base font-bold text-ink-900 font-serif">
                                About the Book (पुस्तक परिचय)
                            </h2>
                            @if ($translation?->short_description)
                                <p class="text-sm font-medium text-slate-800 leading-relaxed italic border-l-2 border-terracotta-500 pl-3">
                                    {{ $translation->short_description }}
                                </p>
                            @endif
                            @if ($translation?->description)
                                <div class="text-sm leading-relaxed text-slate-600 space-y-2">
                                    {!! nl2br(e($translation->description)) !!}
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Summary / Synopsis -->
                    @if ($translation?->summary)
                        <div class="rounded-2xl border border-warm-300 bg-warm-100/60 p-4 sm:p-5">
                            <h2 class="text-xs font-bold text-ink-900 uppercase tracking-wider mb-1.5">
                                Literary Summary &amp; Synopsis (कथानक व सारांश)
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                {!! nl2br(e($translation->summary)) !!}
                            </p>
                        </div>
                    @endif

                    <!-- Why Read This Book (यह पुस्तक क्यों पढ़ें) -->
                    @if ($translation?->why_read)
                        <div class="rounded-2xl border border-sage-200 bg-sage-50/40 p-4 sm:p-5">
                            <h2 class="text-xs font-bold text-sage-900 uppercase tracking-wider mb-1.5">
                                Why Read This Book (यह पुस्तक क्यों पढ़ें)
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                {!! nl2br(e($translation->why_read)) !!}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Column 3 (Right, 3 cols): Compact "About this Edition" Information Panel (Reference 2 Style) -->
                <div class="lg:col-span-3">
                    <div class="rounded-2xl border border-warm-300 bg-white p-5 shadow-sm space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-warm-200 pb-2">
                            Edition &amp; Metadata (ग्रंथ विवरण)
                        </h3>

                        <dl class="space-y-3 text-xs">
                            <div>
                                <dt class="font-medium text-slate-400">Language</dt>
                                <dd class="font-bold text-ink-900 mt-0.5">
                                    {{ $book->originalLanguage?->name ?? 'Hindi (हिन्दी)' }}
                                </dd>
                            </div>

                            @if ($primaryCategory)
                                <div>
                                    <dt class="font-medium text-slate-400">Category</dt>
                                    <dd class="font-bold text-ink-900 mt-0.5">
                                        {{ $categoryName }}
                                    </dd>
                                </div>
                            @endif

                            @if ($book->pages)
                                <div>
                                    <dt class="font-medium text-slate-400">Total Pages</dt>
                                    <dd class="font-bold text-ink-900 mt-0.5">
                                        {{ $book->pages }} pages
                                    </dd>
                                </div>
                            @endif

                            @if ($book->publication_year)
                                <div>
                                    <dt class="font-medium text-slate-400">Publication Year</dt>
                                    <dd class="font-bold text-ink-900 mt-0.5">
                                        {{ $book->publication_year }}
                                    </dd>
                                </div>
                            @endif

                            @if ($book->publisher)
                                <div>
                                    <dt class="font-medium text-slate-400">Publisher</dt>
                                    <dd class="font-semibold text-slate-800 mt-0.5">
                                        {{ $book->publisher }}
                                    </dd>
                                </div>
                            @endif

                            @if ($book->isbn)
                                <div>
                                    <dt class="font-medium text-slate-400">ISBN</dt>
                                    <dd class="font-mono text-slate-700 mt-0.5">
                                        {{ $book->isbn }}
                                    </dd>
                                </div>
                            @endif

                            @if ($book->edition)
                                <div>
                                    <dt class="font-medium text-slate-400">Edition</dt>
                                    <dd class="font-semibold text-slate-800 mt-0.5">
                                        {{ $book->edition }}
                                    </dd>
                                </div>
                            @endif

                            <div>
                                <dt class="font-medium text-slate-400">Rights &amp; Access</dt>
                                <dd class="font-semibold text-slate-800 mt-0.5">
                                    {{ $book->license_type ?? 'Public Domain Heritage' }}
                                </dd>
                            </div>
                        </dl>

                        @if ($pdfMedia)
                            <div class="border-t border-warm-200 pt-3 text-[11px] text-slate-400">
                                Digitized PDF File: <span class="font-mono">{{ round($pdfMedia->size / 1048576, 1) }} MB</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>

    @if (!empty($bookLyrics))
    <!-- Devotional Lyrics & Recitation Section (सम्पूर्ण पाठ, चालीसा व स्तोत्र) -->
    <section id="lyrics-section" class="border-t border-warm-200/80 pt-10" x-data="{
        fontSize: 'base',
        copied: false,
        copyLyrics() {
            const el = document.getElementById('lyrics-content');
            if (el) {
                navigator.clipboard.writeText(el.innerText).then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2500);
                });
            }
        }
    }">
        <x-ui.container size="default">
            <div class="rounded-3xl border border-amber-200/80 bg-gradient-to-b from-amber-50/40 via-white to-warm-50/50 p-6 sm:p-10 shadow-sm">
                <!-- Header & Controls Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-amber-200/60">
                    <div>
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-0.5 text-xs font-bold text-amber-900 mb-2">
                            <span>🕉️ सम्पूर्ण पाठ व चालीसा / स्तोत्र</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-ink-900 font-serif">
                            {{ $title }} — सम्पूर्ण पाठ एवं श्लोक (Complete Lyrics &amp; Recitation)
                        </h2>
                        <p class="mt-1 text-xs sm:text-sm text-slate-600">
                            शुद्ध देवनागरी पाठ, दोहे, चौपाई व नियमित पठन हेतु सुगम फॉन्ट समायोजन के साथ।
                        </p>
                    </div>

                    <!-- Interactive Controls Toolbar -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Font Size Adjustment -->
                        <div class="inline-flex items-center rounded-xl border border-warm-300 bg-white p-1 shadow-2xs text-xs font-semibold text-slate-700">
                            <span class="px-2 text-[11px] text-slate-400 font-medium">फॉन्ट:</span>
                            <button 
                                type="button" 
                                @click="fontSize = 'sm'" 
                                :class="fontSize === 'sm' ? 'bg-amber-100 text-amber-900 font-bold' : 'hover:bg-warm-100 text-slate-600'"
                                class="rounded-lg px-2 py-1 text-xs transition cursor-pointer" 
                                title="Small Text">A-</button>
                            <button 
                                type="button" 
                                @click="fontSize = 'base'" 
                                :class="fontSize === 'base' ? 'bg-amber-100 text-amber-900 font-bold' : 'hover:bg-warm-100 text-slate-600'"
                                class="rounded-lg px-2 py-1 text-xs transition cursor-pointer" 
                                title="Normal Text">A</button>
                            <button 
                                type="button" 
                                @click="fontSize = 'lg'" 
                                :class="fontSize === 'lg' ? 'bg-amber-100 text-amber-900 font-bold' : 'hover:bg-warm-100 text-slate-600'"
                                class="rounded-lg px-2 py-1 text-sm font-bold transition cursor-pointer" 
                                title="Large Text">A+</button>
                            <button 
                                type="button" 
                                @click="fontSize = 'xl'" 
                                :class="fontSize === 'xl' ? 'bg-amber-100 text-amber-900 font-bold' : 'hover:bg-warm-100 text-slate-600'"
                                class="rounded-lg px-2.5 py-1 text-base font-extrabold transition cursor-pointer" 
                                title="Extra Large Text">A++</button>
                        </div>


                        <!-- Copy Verses Button -->
                        <button 
                            type="button" 
                            @click="copyLyrics()" 
                            class="inline-flex items-center gap-1.5 rounded-xl border border-warm-300 bg-white hover:bg-warm-50 px-3.5 py-2 text-xs font-bold text-ink-900 transition shadow-2xs cursor-pointer active:scale-95"
                            title="Copy Verses"
                        >
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                            </svg>
                            <span x-text="copied ? 'कॉपी हो गया! ✓' : 'पाठ कॉपी करें'"></span>
                        </button>
                    </div>
                </div>

                <!-- Lyrics Content Area -->
                <div class="mt-8">
                    <div 
                        id="lyrics-content"
                        :class="{
                            'text-sm leading-relaxed': fontSize === 'sm',
                            'text-base sm:text-lg leading-loose': fontSize === 'base',
                            'text-lg sm:text-xl leading-loose': fontSize === 'lg',
                            'text-xl sm:text-2xl leading-loose': fontSize === 'xl'
                        }"
                        class="devotional-lyrics-container font-serif text-ink-950 font-medium whitespace-pre-line tracking-wide rounded-2xl bg-white/80 border border-warm-200/80 p-6 sm:p-10 shadow-2xs select-text"
                    >{!! e($bookLyrics) !!}</div>
                </div>

                <!-- Recitation Footer Note -->
                <div class="mt-6 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500 pt-4 border-t border-amber-200/40">
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span>प्रामाणिक एवं शुद्ध देवनागरी संस्करण (Verified Traditional Recitation Text)</span>
                    </div>
                    <div>
                        <span>स्रोत: सनातन धर्म ग्रंथ संग्रह • हिन्दी पीडीएफ पुस्तकालय</span>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>
    @endif

    <!-- Author Biography Card (if author present) -->
    @if ($primaryAuthor)
        @php
            $authorTrans = $primaryAuthor->translations->where('locale', 'hi')->first() 
                ?? $primaryAuthor->translations->where('locale', 'en')->first() 
                ?? $primaryAuthor->translations->first();
        @endphp
        @if ($authorTrans?->biography)
            <section class="border-t border-warm-200/80 pt-8">
                <x-ui.container size="default">
                    <div class="rounded-2xl border border-warm-300 bg-white p-6 sm:p-8 shadow-2xs">
                        <div class="flex flex-col sm:flex-row items-start gap-5">
                            <div class="h-16 w-16 shrink-0 rounded-full bg-warm-200 border border-warm-300 text-ink-900 flex items-center justify-center font-bold text-xl font-serif">
                                {{ mb_substr($authorName ?? 'A', 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-3">
                                    <h3 class="text-base font-bold text-ink-900 font-serif">
                                        About Author: {{ $authorName }}
                                    </h3>
                                    <a href="{{ route('authors.show', $primaryAuthor->slug) }}" class="text-xs font-semibold text-terracotta-600 hover:text-terracotta-800 hover:underline">
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

    @if ($book->slug === 'hanuman-chalisa')
    <!-- Frequently Asked Questions (हनुमान चालीसा के सवाल-जवाब) -->
    <section id="faq-section" class="border-t border-warm-200/80 pt-10" x-data="{ activeAccordion: 0 }">
        <x-ui.container size="default">
            <div class="rounded-3xl border border-warm-300 bg-white p-6 sm:p-10 shadow-sm">
                <div class="mb-6">
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-0.5 text-xs font-bold text-amber-900 mb-2">
                        <span>❓ अक्सर पूछे जाने वाले सवाल</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-ink-900 font-serif">
                        हनुमान चालीसा के सवाल-जवाब (Hanuman Chalisa FAQs)
                    </h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-600">
                        हनुमान चालीसा, पाठ के नियम, इतिहास व महत्व से जुड़े प्रमुख प्रश्नों के प्रामाणिक उत्तर।
                    </p>
                </div>

                <div class="space-y-3">
                    @php
                        $faqs = [
                            [
                                'q' => 'हनुमान चालीसा किसने लिखी?',
                                'a' => 'गोस्वामी तुलसीदास जी के द्वारा 16वीं शताब्दी में काशी (वाराणसी) के संकटमोचन क्षेत्र में अवधी भाषा में हनुमान चालीसा लिखी गई थी।'
                            ],
                            [
                                'q' => 'हनुमान चालीसा में कितने दोहे और चौपाइयां हैं?',
                                'a' => 'हनुमान चालीसा में 2 प्रारंभिक दोहे, 40 चौपाइयां, और 1 समापन दोहा — यानी कुल 3 दोहे और 40 चौपाइयां हैं।'
                            ],
                            [
                                'q' => 'हनुमान जी के बचपन का क्या नाम था?',
                                'a' => 'हनुमान जी के बचपन का नाम मारुति था।'
                            ],
                            [
                                'q' => 'हनुमान जी के 5 अवतार (पंचमुखी स्वरूप) कौन-से हैं?',
                                'a' => 'पंचमुखी हनुमान जी के 5 मुख हैं: १. श्री हनुमान मुख (वानर मुख - पूर्व), २. नरसिंह मुख (दक्षिण), ३. गरुड़ मुख (पश्चिम), ४. वराह मुख (उत्तर), और ५. हयग्रीव मुख (अश्व मुख - ऊर्ध्व)।'
                            ],
                            [
                                'q' => 'हनुमान चालीसा का पाठ करने का क्या महत्व है?',
                                'a' => 'हनुमान चालीसा का नियमित पाठ करने से भय, चिंता, रोग-पीड़ा और नकारात्मक ऊर्जा का नाश होता है। "नासै रोग हरै सब पीरा। जपत निरंतर हनुमत बीरा।।" मंगलवार और शनिवार को पाठ करने से मारुतिनंदन की विशेष कृपा प्राप्त होती है।'
                            ],
                            [
                                'q' => 'क्या महिलाएं हनुमान चालीसा का पाठ कर सकती हैं?',
                                'a' => 'हाँ, महिलाएँ व माताएँ पूरी श्रद्धा, पवित्रता व भक्ति-भाव से श्री हनुमान चालीसा का पाठ कर सकती हैं।'
                            ]
                        ];
                    @endphp

                    @foreach ($faqs as $i => $faq)
                    <div class="rounded-xl border border-warm-200 overflow-hidden">
                        <button 
                            type="button" 
                            @click="activeAccordion = activeAccordion === {{ $i }} ? null : {{ $i }}"
                            class="w-full flex items-center justify-between p-4 sm:p-5 text-left bg-warm-50/60 hover:bg-warm-100/60 transition cursor-pointer"
                        >
                            <span class="font-bold text-ink-900 text-sm sm:text-base font-serif">{{ $faq['q'] }}</span>
                            <svg 
                                class="h-5 w-5 text-slate-500 transform transition-transform duration-200 shrink-0 ml-3"
                                :class="{ 'rotate-180': activeAccordion === {{ $i }} }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div 
                            x-show="activeAccordion === {{ $i }}" 
                            x-collapse
                            class="p-4 sm:p-5 bg-white border-t border-warm-200 text-xs sm:text-sm text-slate-700 leading-relaxed"
                        >
                            {{ $faq['a'] }}
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Devotional Cross-Links (Beating Competitor's Bottom Links) -->
                <div class="mt-8 pt-6 border-t border-warm-200">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">
                        संबंधित स्तोत्र एवं पाठ (Related Devotional Prayers)
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('books.show', 'hanuman-chalisa') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-900 hover:bg-amber-100 transition">
                            <span>🚩 श्री हनुमान चालीसा पाठ</span>
                        </a>
                        <a href="{{ route('ved-puran') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-warm-300 bg-warm-100/60 px-3 py-1.5 text-xs font-semibold text-ink-900 hover:bg-warm-200 transition">
                            <span>📖 वेद एवं पुराण संग्रह</span>
                        </a>
                        <a href="{{ route('books.show', 'ramcharitmanas') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-warm-300 bg-warm-100/60 px-3 py-1.5 text-xs font-semibold text-ink-900 hover:bg-warm-200 transition">
                            <span>🪔 संपूर्ण श्रीरामचरितमानस</span>
                        </a>
                        <a href="{{ route('books.show', 'bhagavad-gita') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-warm-300 bg-warm-100/60 px-3 py-1.5 text-xs font-semibold text-ink-900 hover:bg-warm-200 transition">
                            <span>🕉️ श्रीमद्भगवद्गीता (मूल श्लोक व अर्थ)</span>
                        </a>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>
    @endif

    <!-- Community Ratings & Reviews Section -->
    <section id="reviews-section" class="border-t border-warm-200/80 pt-10">
        <x-ui.container size="default">
            <div class="mb-8">
                <h2 class="text-xl sm:text-2xl font-bold text-ink-900 font-serif">
                    Reader Reviews &amp; Ratings (पाठक समीक्षा व रेटिंग)
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Authentic ratings and moderated literary reflections from verified community readers.
                </p>
            </div>

            <!-- Rating Overview & Distribution Grid -->
            <div class="rounded-2xl border border-warm-300 bg-white p-6 sm:p-8 shadow-2xs mb-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                    <!-- Left: Average Rating Number -->
                    <div class="md:col-span-4 flex flex-col items-center justify-center text-center sm:border-r sm:border-warm-200 sm:pr-6">
                        <span class="text-5xl font-extrabold text-ink-900 tracking-tight font-serif">
                            {{ number_format($book->rating_average, 1) }}
                        </span>
                        <div class="mt-2">
                            <x-rating-stars :rating="$book->rating_average" :showCount="false" size="md" />
                        </div>
                        <p class="mt-2 text-xs text-slate-500 font-medium">
                            Based on {{ number_format($book->rating_count) }} {{ $book->rating_count === 1 ? 'rating' : 'ratings' }}
                            @if ($book->review_count > 0)
                                &bull; {{ number_format($book->review_count) }} {{ $book->review_count === 1 ? 'review' : 'reviews' }}
                            @endif
                        </p>
                    </div>

                    <!-- Right: 1-5 Star Breakdown Bars -->
                    <div class="md:col-span-8 space-y-2">
                        @foreach ([5, 4, 3, 2, 1] as $stars)
                            @php
                                $starCount = $ratingDistribution[$stars] ?? 0;
                                $percentage = $book->rating_count > 0 ? round(($starCount / $book->rating_count) * 100) : 0;
                            @endphp
                            <div class="flex items-center gap-3 text-xs">
                                <span class="w-8 shrink-0 font-bold text-slate-700 flex items-center justify-end gap-0.5">
                                    {{ $stars }} <span class="text-amber-400">★</span>
                                </span>
                                <div class="flex-1 h-2.5 rounded-full bg-warm-100 overflow-hidden">
                                    <div 
                                        class="h-full rounded-full bg-amber-400 transition-all duration-500" 
                                        style="width: {{ $percentage }}%"
                                        role="progressbar"
                                        aria-valuenow="{{ $percentage }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        aria-label="{{ $stars }} star rating distribution: {{ $percentage }}%"
                                    ></div>
                                </div>
                                <span class="w-12 shrink-0 text-right text-slate-400 font-mono text-[11px]">
                                    {{ $starCount }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Livewire Rating & Review Submission Form -->
            <div class="mb-10">
                <livewire:book-review-form :book="$book" />
            </div>

            <!-- Public Approved Reviews List -->
            <div class="pt-6 border-t border-warm-200">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-base font-bold text-ink-900 font-serif">
                        Community Reviews ({{ $reviews->total() }})
                    </h3>
                </div>

                @if ($reviews->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($reviews as $review)
                            <x-review-card :review="$review" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $reviews->links() }}
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-warm-300 bg-white/50 p-8 text-center">
                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-warm-100 text-slate-400 mb-3">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-800">No community reviews published yet</h4>
                        <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                            Be the first reader to rate this title and share your perspective using the form above.
                        </p>
                    </div>
                @endif
            </div>
        </x-ui.container>
    </section>

    <!-- Divine Topic Cluster: Related Prayers, Chalisas, Aartis & Stotras -->
    @if (!empty($relatedPrayers) && $relatedPrayers->isNotEmpty())
        <section class="border-t border-amber-200/80 pt-10 bg-gradient-to-b from-amber-50/40 via-white to-transparent">
            <x-ui.container size="default">
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-0.5 text-xs font-bold text-amber-900 mb-1.5">
                            <span>🕉️ पावन स्तोत्र व आराधना संग्रह (Devotional Prayers &amp; Hymns)</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-bold text-ink-900 font-serif">
                            {{ $book->deity_label ?? $title }} — संबंधित चालीसा, आरती व स्तोत्र पाठ
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1">
                            नित्य पाठ, शुद्ध देवनागरी बोल, ऑडियो वाचन एवं अर्थ सहित।
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($relatedPrayers as $prayer)
                        @php
                            $pTitle = $prayer->localized()?->title ?? $prayer->slug;
                            $pDesc = $prayer->localized()?->short_description ?? \Illuminate\Support\Str::limit(strip_tags($prayer->localized()?->description ?? ''), 90);
                        @endphp
                        <div class="rounded-2xl border border-amber-200 bg-white p-5 shadow-2xs hover:shadow-md transition hover:border-amber-400 flex flex-col justify-between group">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="inline-flex items-center rounded-lg bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold text-amber-900">
                                        {{ $prayer->type_label }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-medium flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5 text-amber-500 fill-current" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                                        ऑडियो सहित
                                    </span>
                                </div>
                                <h3 class="font-bold text-base text-ink-900 font-serif group-hover:text-amber-700 transition line-clamp-1">
                                    <a href="{{ route('books.show', $prayer->slug) }}">
                                        {{ $pTitle }}
                                    </a>
                                </h3>
                                @if ($pDesc)
                                    <p class="mt-1.5 text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                        {{ $pDesc }}
                                    </p>
                                @endif
                            </div>
                            <div class="mt-4 pt-3 border-t border-warm-100 flex items-center justify-between">
                                <a href="{{ route('books.show', $prayer->slug) }}" class="text-xs font-bold text-amber-800 hover:text-amber-950 flex items-center gap-1">
                                    <span>संपूर्ण पाठ पढ़ें</span> &rarr;
                                </a>
                                <a href="{{ route('books.show', $prayer->slug) }}#lyrics-section" class="inline-flex items-center gap-1 rounded-lg bg-amber-50 hover:bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-900 transition">
                                    <span>🎧 सुनें</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.container>
        </section>
    @endif

    <!-- Divine Topic Cluster: Related Holy Books & Scriptures (PDFs) -->
    @if (!empty($relatedDeityBooks) && $relatedDeityBooks->isNotEmpty())
        <section class="border-t border-warm-200/80 pt-10">
            <x-ui.container size="default">
                <x-ui.section-header 
                    title="{{ $book->deity_label ?? $title }} — संबंधित पावन ग्रंथ व पुस्तकें (Holy Scriptures &amp; PDFs)"
                    subtitle="Explore complete digitized scriptures, commentaries, and books available for 3D flip reading and PDF download."
                />

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($relatedDeityBooks as $deityBook)
                        <x-book-card :book="$deityBook" />
                    @endforeach
                </div>
            </x-ui.container>
        </section>
    @endif

    <!-- More by this Author -->
    @if ($authorBooks->isNotEmpty())
        <section class="border-t border-warm-200/80 pt-10">
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
        <section class="border-t border-warm-200/80 pt-10">
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
    <section class="border-t border-warm-200/80 pt-10">
        <x-ui.container size="default">
            <div class="max-w-3xl">
                <h2 class="text-xl font-bold text-ink-900 font-serif mb-4">
                    Frequently Asked Questions (अक्सर पूछे जाने वाले प्रश्न)
                </h2>

                <div class="space-y-3" x-data="{ active: null }">
                    <div class="rounded-xl border border-warm-300 bg-white p-4 shadow-2xs">
                        <button 
                            @click="active = (active === 1 ? null : 1)" 
                            class="flex w-full items-center justify-between text-left text-sm font-semibold text-ink-900"
                        >
                            <span>Is this edition of {{ $title }} free to read and download?</span>
                            <span class="text-slate-400 font-bold" x-text="active === 1 ? '−' : '+'"></span>
                        </button>
                        <div x-show="active === 1" x-collapse class="mt-2 text-xs text-slate-600 leading-relaxed">
                            Yes. The Hindi PDF Library provides open, free access to public-domain cultural works and verified open-access materials for academic, cultural, and personal reading.
                        </div>
                    </div>

                    <div class="rounded-xl border border-warm-300 bg-white p-4 shadow-2xs">
                        <button 
                            @click="active = (active === 2 ? null : 2)" 
                            class="flex w-full items-center justify-between text-left text-sm font-semibold text-ink-900"
                        >
                            <span>What formats are supported for reading?</span>
                            <span class="text-slate-400 font-bold" x-text="active === 2 ? '−' : '+'"></span>
                        </button>
                        <div x-show="active === 2" x-collapse class="mt-2 text-xs text-slate-600 leading-relaxed">
                            Digitized documents are provided in standard PDF format suitable for desktop screens, mobile devices, tablets, and e-readers.
                        </div>
                    </div>

                    <div class="rounded-xl border border-warm-300 bg-white p-4 shadow-2xs">
                        <button 
                            @click="active = (active === 3 ? null : 3)" 
                            class="flex w-full items-center justify-between text-left text-sm font-semibold text-ink-900"
                        >
                            <span>How can I contribute corrections or report bibliographic errors?</span>
                            <span class="text-slate-400 font-bold" x-text="active === 3 ? '−' : '+'"></span>
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
