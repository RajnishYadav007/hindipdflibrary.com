@extends('layouts.base')

@section('content')
<div class="space-y-16 pb-24 lg:pb-16" x-data="{
    // Audiobook Player State
    activeTrack: {
        title: '{{ addslashes($audioBooks->first()?->localized()?->title ?? 'श्री हनुमान चालीसा') }}',
        author: '{{ addslashes($audioBooks->first()?->authors->first()?->localized()?->name ?? 'गोस्वामी तुलसीदास') }}',
        slug: '{{ $audioBooks->first()?->slug ?? 'hanuman-chalisa' }}',
        coverUrl: '{{ $audioBooks->first()?->cover_url ?? '' }}',
        text: '{{ addslashes(preg_replace('/\s+/', ' ', Str::limit($audioBooks->first()?->localized()?->lyrics ?? $audioBooks->first()?->localized()?->description ?? 'श्री गुरु चरण सरोज रज निज मनु मुकुरु सुधारि। बरनऊं रघुबर बिमल जसु जो दायकु फल चारि॥', 800))) }}'
    },
    isPlaying: false,
    playbackSpeed: '1.0x',
    currentTime: '02:14',
    totalTime: '09:48',
    progress: 25,
    togglePlay() {
        this.isPlaying = !this.isPlaying;
        window.dispatchEvent(new CustomEvent('play-book-audio', {
            detail: {
                title: this.activeTrack.title,
                author: this.activeTrack.author,
                text: this.activeTrack.text,
                lang: 'hi'
            }
        }));
    },
    selectTrack(title, author, slug, coverUrl, text) {
        this.activeTrack = { title, author, slug, coverUrl, text };
        this.isPlaying = true;
        window.dispatchEvent(new CustomEvent('play-book-audio', {
            detail: { title, author, text, lang: 'hi' }
        }));
    },
    cycleSpeed() {
        const speeds = ['1.0x', '1.2x', '1.5x'];
        const next = (speeds.indexOf(this.playbackSpeed) + 1) % speeds.length;
        this.playbackSpeed = speeds[next];
    },
    // Featured Books Tab
    featuredTab: 'popular',
    // Children Age Filter
    childrenAge: 'all'
}">

    <!-- ============================================================== -->
    <!-- SECTION 1: HERO SECTION                                        -->
    <!-- Warm ivory background, Editorial typography, Search, 3D Books  -->
    <!-- ============================================================== -->
    <section class="relative overflow-hidden bg-gradient-to-b from-[#fbf8f3] via-[#f7f3ec] to-[#f4eee4] border-b border-orange-950/10 py-12 lg:py-16">
        <div class="absolute inset-0 bg-[radial-gradient(#e5ded2_1px,transparent_1px)] [background-size:24px_24px] opacity-40 pointer-events-none"></div>
        <x-ui.container size="default" class="relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Left: Headline, Search & Editorial Intro -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <!-- Heritage Trust Badge -->
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/90 backdrop-blur-xs px-3.5 py-1.5 text-xs font-semibold text-stone-800 border border-stone-200 shadow-2xs">
                        <span class="flex h-2 w-2 rounded-full bg-orange-600 animate-pulse"></span>
                        <span>{{ config('app.name', 'Hindi PDF Library') }} • 100% निःशुल्क</span>
                    </div>

                    <!-- Main Headline -->
                    <div>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight font-serif">
                            साहित्य और ज्ञान का <span class="text-orange-700 underline decoration-orange-300 decoration-wavy underline-offset-4">पावन संगम</span> — अब आपके हाथों में
                        </h1>
                        <p class="mt-2 text-xs sm:text-sm font-sans font-medium text-slate-500">
                            Discover, Read &amp; Study Classic Hindi Literature
                        </p>
                        <p class="mt-3 text-base sm:text-lg font-sans text-slate-600 leading-relaxed max-w-xl mx-auto lg:mx-0">
                            हज़ारों कालजयी हिंदी पुस्तकें, पावन चालीसा, बाल साहित्य और ऑडियोबुक्स — बिना किसी शुल्क के ऑनलाइन पढ़ें, सुनें और संरक्षित PDF डाउनलोड करें।
                        </p>
                    </div>

                    <!-- Universal Search Box -->
                    <div class="pt-1 max-w-xl mx-auto lg:mx-0">
                        <form action="{{ route('search') }}" method="GET" class="relative flex items-center">
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    name="q" 
                                    placeholder="पुस्तक का नाम, लेखक या विषय खोजें... (Search books, authors)" 
                                    aria-label="Search books, authors"
                                    class="w-full rounded-2xl border border-stone-300 bg-white py-3.5 pl-11 pr-28 text-sm text-slate-800 placeholder:text-slate-400 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/20 shadow-sm transition"
                                />
                                <div class="absolute inset-y-1 right-1 flex items-center">
                                    <button 
                                        type="submit" 
                                        class="h-full px-5 rounded-xl bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white font-semibold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer"
                                    >
                                        <span>खोजें</span>
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Trending Search Chips -->
                        <div class="mt-3.5 flex flex-wrap items-center justify-center lg:justify-start gap-1.5 text-xs">
                            <span class="font-semibold text-slate-400 mr-1 flex items-center gap-1">
                                <span>🔥</span> लोकप्रिय:
                            </span>
                            <a href="{{ route('books.show', 'hanuman-chalisa') }}" class="rounded-full bg-white hover:bg-orange-50 px-3 py-1 text-slate-700 hover:text-orange-700 border border-stone-200 shadow-2xs transition font-medium">हनुमान चालीसा</a>
                            <a href="{{ route('books.show', 'bhagavad-gita') }}" class="rounded-full bg-white hover:bg-orange-50 px-3 py-1 text-slate-700 hover:text-orange-700 border border-stone-200 shadow-2xs transition font-medium">श्रीमद्भगवद्गीता</a>
                            <a href="{{ route('books.show', 'panchatantra-ki-kahaniyan') }}" class="rounded-full bg-white hover:bg-orange-50 px-3 py-1 text-slate-700 hover:text-orange-700 border border-stone-200 shadow-2xs transition font-medium">पंचतंत्र</a>
                            <a href="{{ route('search', ['q' => 'गोदान']) }}" class="rounded-full bg-white hover:bg-orange-50 px-3 py-1 text-slate-700 hover:text-orange-700 border border-stone-200 shadow-2xs transition font-medium">गोदान (प्रेमचंद)</a>
                            <a href="{{ route('ved-puran') }}" class="rounded-full bg-white hover:bg-orange-50 px-3 py-1 text-slate-700 hover:text-orange-700 border border-stone-200 shadow-2xs transition font-medium">वेद एवं पुराण</a>
                        </div>
                    </div>

                    <!-- Literary Quote Box (Kindle/Editorial Style) -->
                    <div class="pt-2 max-w-xl mx-auto lg:mx-0">
                        <div class="rounded-2xl border border-stone-200/90 bg-white/80 backdrop-blur-xs p-4 shadow-2xs flex items-start gap-3.5">
                            <span class="text-3xl text-orange-400 font-serif leading-none shrink-0 select-none">“</span>
                            <div>
                                <p class="text-xs sm:text-sm text-slate-700 italic font-serif leading-relaxed">
                                    एक अच्छी पुस्तक हज़ार दोस्तों के बराबर होती है, जबकि एक अच्छा दोस्त एक संपूर्ण पुस्तकालय के बराबर होता है।
                                </p>
                                <p class="text-[11px] font-bold text-slate-500 mt-1 uppercase tracking-wider">
                                    — डॉ. ए.पी.जे. अब्दुल कलाम
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: High-End Layered 3D Books Composition with Floating Badges -->
                <div class="lg:col-span-5 flex justify-center items-center relative py-6">
                    <div class="relative w-80 h-96 flex items-center justify-center">
                        @php
                            $heroBooksList = $heroBooks ?? ($featuredBooks->take(3));
                        @endphp

                        @if ($heroBooksList->count() >= 3)
                            <!-- Left Book: Angled -8deg -->
                            @php $b1 = $heroBooksList[1]; @endphp
                            <a href="{{ route('books.show', $b1->slug) }}" class="absolute left-0 top-6 w-44 aspect-[2/3] rounded-2xl overflow-hidden shadow-2xl border-2 border-white -rotate-6 transition-transform duration-300 hover:scale-105 hover:z-30 group" title="{{ $b1->title }}">
                                @if ($b1->cover_url)
                                    <img src="{{ $b1->cover_url }}" alt="{{ $b1->title }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-amber-900 to-stone-900 p-4 text-center text-white flex flex-col justify-between">
                                        <span class="text-[9px] uppercase tracking-widest text-amber-300 font-bold">DIGITAL HERITAGE</span>
                                        <span class="font-serif text-xs font-bold leading-tight line-clamp-3">{{ $b1->title }}</span>
                                        <span class="text-[9px] text-amber-200">{{ $b1->authors->first()?->name }}</span>
                                    </div>
                                @endif
                            </a>

                            <!-- Right Book: Angled +8deg -->
                            @php $b2 = $heroBooksList[2]; @endphp
                            <a href="{{ route('books.show', $b2->slug) }}" class="absolute right-0 top-10 w-44 aspect-[2/3] rounded-2xl overflow-hidden shadow-2xl border-2 border-white rotate-6 transition-transform duration-300 hover:scale-105 hover:z-30 group" title="{{ $b2->title }}">
                                @if ($b2->cover_url)
                                    <img src="{{ $b2->cover_url }}" alt="{{ $b2->title }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-slate-900 to-stone-800 p-4 text-center text-white flex flex-col justify-between">
                                        <span class="text-[9px] uppercase tracking-widest text-orange-300 font-bold">CLASSIC</span>
                                        <span class="font-serif text-xs font-bold leading-tight line-clamp-3">{{ $b2->title }}</span>
                                        <span class="text-[9px] text-stone-300">{{ $b2->authors->first()?->name }}</span>
                                    </div>
                                @endif
                            </a>

                            <!-- Center Book: Prominent & Elevated -->
                            @php $b0 = $heroBooksList[0]; @endphp
                            <a href="{{ route('books.show', $b0->slug) }}" class="relative z-20 w-48 aspect-[2/3] rounded-2xl overflow-hidden shadow-2xl border-2 border-white transition-transform duration-300 hover:scale-105 group" title="{{ $b0->title }}">
                                @if ($b0->cover_url)
                                    <img src="{{ $b0->cover_url }}" alt="{{ $b0->title }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-orange-950 via-amber-900 to-stone-900 p-4 text-center text-white flex flex-col justify-between">
                                        <span class="text-[10px] uppercase tracking-widest text-amber-300 font-bold">FEATURED MASTERWORK</span>
                                        <span class="font-serif text-sm font-bold leading-tight line-clamp-3">{{ $b0->title }}</span>
                                        <span class="text-[10px] text-amber-200">{{ $b0->authors->first()?->name }}</span>
                                    </div>
                                @endif
                            </a>

                            <!-- Floating Badge 1: Audiobooks Available (Kindle/Audible Style) -->
                            <div class="absolute -bottom-2 left-2 z-30 inline-flex items-center gap-2 rounded-2xl bg-white/95 backdrop-blur-md px-3.5 py-2 shadow-lg border border-stone-200">
                                <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                                    </svg>
                                </span>
                                <div>
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider leading-none">ऑडियो सुविधा</p>
                                    <p class="text-xs font-bold text-slate-800 leading-tight mt-0.5">ऑडियोबुक्स उपलब्ध</p>
                                </div>
                            </div>

                            <!-- Floating Badge 2: Reader Satisfaction -->
                            <div class="absolute -top-3 right-0 z-30 inline-flex items-center gap-1.5 rounded-2xl bg-white/95 backdrop-blur-md px-3 py-1.5 shadow-lg border border-stone-200 text-xs font-bold text-slate-800">
                                <span class="text-amber-500">★</span>
                                <span>4.9 / 5.0</span>
                                <span class="text-[10px] font-normal text-slate-500">(10k+ पाठक)</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 2: THE 4 CORE PILLARS GRID                             -->
    <!-- Books, Audiobooks, Chalisas, Children Stories                  -->
    <!-- ============================================================== -->
    <section id="core-pillars">
        <x-ui.container size="default">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Pillar 1: Books (PDF) -->
                <a href="{{ route('books.index') }}" class="group rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs hover:shadow-md hover:border-orange-300 transition flex flex-col justify-between">
                    <div>
                        <div class="h-11 w-11 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mb-3.5 group-hover:scale-105 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-orange-700 transition font-serif">
                            मुफ़्त PDF पुस्तकें
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            कालजयी उपन्यास, कविताएँ व वैचारिक साहित्य 3D रीडर और डाउनलोड सहित।
                        </p>
                    </div>
                    <span class="mt-3.5 inline-flex items-center text-xs font-bold text-orange-600 group-hover:translate-x-1 transition">
                        पुस्तकालय देखें &rarr;
                    </span>
                </a>

                <!-- Pillar 2: Audiobooks -->
                <a href="#audiobooks-hub" class="group rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs hover:shadow-md hover:border-amber-300 transition flex flex-col justify-between">
                    <div>
                        <div class="h-11 w-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3.5 group-hover:scale-105 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.757 3.63 8.25 4.51 8.25H6.75z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-amber-700 transition font-serif">
                            ऑडियो बुक्स व वाचन
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Listen to Books — नेत्रों को विश्राम दें, 1-क्लिक इन-पेज हिंदी ऑडियो प्लेयर से कहानियाँ व पाठ सुनें।
                        </p>
                    </div>
                    <span class="mt-3.5 inline-flex items-center text-xs font-bold text-amber-600 group-hover:translate-x-1 transition">
                        ऑडियो सुनें &rarr;
                    </span>
                </a>

                <!-- Pillar 3: Chalisas & Aartis -->
                <a href="{{ route('categories.show', 'chalisa-stotra') }}" class="group rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs hover:shadow-md hover:border-orange-300 transition flex flex-col justify-between">
                    <div>
                        <div class="h-11 w-11 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mb-3.5 group-hover:scale-105 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-orange-800 transition font-serif">
                            चालीसा, आरती व स्तोत्र
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            श्री हनुमान चालीसा, आरती, स्तोत्र व वेद-पुराण शुद्ध देवनागरी बोल व ऑडियो सहित।
                        </p>
                    </div>
                    <span class="mt-3.5 inline-flex items-center text-xs font-bold text-orange-700 group-hover:translate-x-1 transition">
                        आराधना पाठ &rarr;
                    </span>
                </a>

                <!-- Pillar 4: Children Stories -->
                <a href="#children-stories-section" class="group rounded-2xl border border-stone-200/90 bg-white p-5 shadow-2xs hover:shadow-md hover:border-emerald-300 transition flex flex-col justify-between">
                    <div>
                        <div class="h-11 w-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3.5 group-hover:scale-105 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-emerald-800 transition font-serif">
                            बाल साहित्य व कहानियाँ
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            पंचतंत्र की प्रेरक कथाएँ, अकबर-बीरबल, तेनालीराम व हितोपदेश की अमर नीति कथाएँ।
                        </p>
                    </div>
                    <span class="mt-3.5 inline-flex items-center text-xs font-bold text-emerald-700 group-hover:translate-x-1 transition">
                        कहानियाँ पढ़ें &rarr;
                    </span>
                </a>
            </div>
        </x-ui.container>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 3: SPLIT SECTION                                       -->
    <!-- Left: विशेष पुस्तकें (Featured Literature)                     -->
    <!-- Right: आज ही सुनना शुरू करें (Kindle/Audible Audiobook Hub)    -->
    <!-- ============================================================== -->
    <section id="audiobooks-hub" class="border-t border-stone-200/80 pt-10">
        <x-ui.container size="default">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left Column: विशेष पुस्तकें (Featured Literature) - 7 Cols -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-0.5 text-xs font-bold text-orange-900 mb-2">
                                <span>📚 Featured Literature • Handpicked Classics</span>
                            </div>
                            <h2 class="text-2xl font-extrabold text-slate-900 font-serif">
                                Featured Literature (विशेष पुस्तकें)
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                उच्च कोटि का कालजयी हिंदी साहित्य एवं ज्ञानवर्धक कृतियाँ
                            </p>
                        </div>

                        <a href="{{ route('books.index') }}" class="text-xs font-bold text-orange-700 hover:text-orange-900 transition shrink-0 flex items-center gap-1">
                            <span>सभी पुस्तकें देखें</span>
                            <span>&rarr;</span>
                        </a>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar text-xs font-semibold">
                        <button 
                            type="button" 
                            @click="featuredTab = 'popular'" 
                            :class="featuredTab === 'popular' ? 'bg-orange-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-stone-200 hover:bg-stone-50'"
                            class="px-3.5 py-1.5 rounded-xl transition cursor-pointer"
                        >
                            लोकप्रिय
                        </button>
                        <button 
                            type="button" 
                            @click="featuredTab = 'latest'" 
                            :class="featuredTab === 'latest' ? 'bg-orange-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-stone-200 hover:bg-stone-50'"
                            class="px-3.5 py-1.5 rounded-xl transition cursor-pointer"
                        >
                            नवीनतम
                        </button>
                        <button 
                            type="button" 
                            @click="featuredTab = 'most_read'" 
                            :class="featuredTab === 'most_read' ? 'bg-orange-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-stone-200 hover:bg-stone-50'"
                            class="px-3.5 py-1.5 rounded-xl transition cursor-pointer"
                        >
                            सर्वाधिक पढ़ी गईं
                        </button>
                        <button 
                            type="button" 
                            @click="featuredTab = 'editors_pick'" 
                            :class="featuredTab === 'editors_pick' ? 'bg-orange-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-stone-200 hover:bg-stone-50'"
                            class="px-3.5 py-1.5 rounded-xl transition cursor-pointer"
                        >
                            संपादक की पसंद
                        </button>
                    </div>

                    <!-- Book Cards Grid (4 Books in 2x2 layout) -->
                    @if ($featuredBooks->isNotEmpty())
                        <div class="grid grid-cols-2 gap-4 sm:gap-5">
                            @foreach ($featuredBooks->take(4) as $fBook)
                                @php
                                    $fTitle = $fBook->localized()?->title ?? $fBook->slug;
                                    $fAuthor = $fBook->authors->first()?->localized()?->name ?? $fBook->authors->first()?->slug ?? 'कालजयी रचनाकार';
                                    $fCategory = $fBook->categories->first()?->localized()?->name ?? 'साहित्य';
                                @endphp
                                <div class="group rounded-2xl border border-stone-200 bg-white p-3 sm:p-4 shadow-2xs hover:shadow-md hover:border-orange-300 transition flex flex-col justify-between">
                                    <div class="relative w-full aspect-[2/3] overflow-hidden rounded-xl bg-slate-900 shadow-inner mb-3">
                                        @if ($fBook->cover_url)
                                            <img src="{{ $fBook->cover_url }}" alt="{{ $fTitle }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" />
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-stone-900 to-amber-950 p-3 text-center text-white flex flex-col justify-between">
                                                <span class="text-[8px] uppercase tracking-widest text-amber-400 font-bold">DIGITAL</span>
                                                <span class="font-serif text-xs font-bold line-clamp-3">{{ $fTitle }}</span>
                                                <span class="text-[8px] text-stone-300">{{ $fAuthor }}</span>
                                            </div>
                                        @endif
                                        <!-- Badge -->
                                        <div class="absolute top-2 left-2 inline-flex items-center gap-1 rounded-md bg-white/90 backdrop-blur-xs px-2 py-0.5 text-[10px] font-bold text-slate-800 shadow-xs">
                                            <span class="text-amber-500">★</span>
                                            <span>{{ number_format($fBook->rating_average ?: 4.8, 1) }}</span>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600">
                                            {{ $fCategory }}
                                        </span>
                                        <h3 class="font-bold text-sm text-slate-900 font-serif group-hover:text-orange-700 transition line-clamp-1 leading-snug mt-0.5">
                                            <a href="{{ route('books.show', $fBook->slug) }}">
                                                {{ $fTitle }}
                                            </a>
                                        </h3>
                                        <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">
                                            {{ $fAuthor }}
                                        </p>
                                    </div>

                                    <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-xs">
                                        <span class="text-slate-400 text-[11px]">
                                            {{ $fBook->pages ? $fBook->pages . ' पृष्ठ' : 'पूर्ण पुस्तक' }}
                                        </span>
                                        <a href="{{ route('books.show', $fBook->slug) }}" class="font-bold text-orange-600 hover:text-orange-700 transition">
                                            पढ़ें &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
                            <p class="text-xs text-slate-500">Featured Literature is being cataloged.</p>
                        </div>
                    @endif
                </div>

                <!-- Right Column: आज ही सुनना शुरू करें (Audiobook Hub) - 5 Cols -->
                <div class="lg:col-span-5">
                    <div class="rounded-3xl bg-gradient-to-br from-slate-900 via-[#161f30] to-slate-950 text-white p-6 sm:p-7 shadow-xl border border-slate-800 space-y-6">
                        <!-- Card Header -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-orange-600/20 text-orange-400 border border-orange-500/30">
                                    <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="text-base font-bold font-serif leading-tight">
                                        आज ही सुनना शुरू करें
                                    </h3>
                                    <p class="text-[11px] text-slate-400">ऑडियोबुक्स व वाचन हब</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 rounded-full bg-orange-500/20 px-2.5 py-0.5 text-[10px] font-bold text-orange-300 border border-orange-500/30">
                                <span class="h-1.5 w-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                                लाइव प्लेयर
                            </span>
                        </div>

                        <!-- Active Audiobook Player Box -->
                        <div class="rounded-2xl bg-white/5 border border-white/10 p-4 space-y-4">
                            <div class="flex items-center gap-3.5">
                                <div class="w-14 h-18 shrink-0 rounded-xl overflow-hidden bg-slate-800 border border-white/10 shadow-sm flex items-center justify-center">
                                    <template x-if="activeTrack.coverUrl">
                                        <img :src="activeTrack.coverUrl" :alt="activeTrack.title" class="w-full h-full object-cover" />
                                    </template>
                                    <template x-if="!activeTrack.coverUrl">
                                        <span class="text-xl">🎧</span>
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[10px] font-semibold text-orange-400 uppercase tracking-wider">अब बज रहा है</p>
                                    <h4 class="text-sm font-bold text-white truncate mt-0.5" x-text="activeTrack.title"></h4>
                                    <p class="text-xs text-slate-400 truncate" x-text="activeTrack.author"></p>
                                </div>
                            </div>

                            <!-- Animated Waveform Visualizer -->
                            <div class="flex items-center justify-center gap-1 h-8 px-2 py-1 bg-black/20 rounded-xl">
                                <div class="w-1 bg-orange-500 rounded-full transition-all duration-300" :class="isPlaying ? 'h-5 animate-pulse' : 'h-1'"></div>
                                <div class="w-1 bg-orange-400 rounded-full transition-all duration-300" :class="isPlaying ? 'h-7 animate-bounce' : 'h-1'"></div>
                                <div class="w-1 bg-amber-400 rounded-full transition-all duration-300" :class="isPlaying ? 'h-4 animate-pulse' : 'h-1'"></div>
                                <div class="w-1 bg-orange-500 rounded-full transition-all duration-300" :class="isPlaying ? 'h-6 animate-bounce' : 'h-1'"></div>
                                <div class="w-1 bg-amber-500 rounded-full transition-all duration-300" :class="isPlaying ? 'h-3 animate-pulse' : 'h-1'"></div>
                                <div class="w-1 bg-orange-400 rounded-full transition-all duration-300" :class="isPlaying ? 'h-7 animate-bounce' : 'h-1'"></div>
                                <div class="w-1 bg-orange-500 rounded-full transition-all duration-300" :class="isPlaying ? 'h-5 animate-pulse' : 'h-1'"></div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="space-y-1">
                                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden cursor-pointer">
                                    <div class="h-full bg-gradient-to-r from-orange-500 to-amber-500 rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                                </div>
                                <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono">
                                    <span x-text="currentTime">02:14</span>
                                    <span x-text="totalTime">09:48</span>
                                </div>
                            </div>

                            <!-- Player Controls -->
                            <div class="flex items-center justify-between pt-1">
                                <!-- Speed Toggle -->
                                <button 
                                    type="button" 
                                    @click="cycleSpeed()" 
                                    class="rounded-lg bg-white/10 px-2 py-1 text-[11px] font-mono text-slate-300 hover:text-white hover:bg-white/20 transition cursor-pointer"
                                    title="प्लेबैक गति बदलें"
                                >
                                    <span x-text="playbackSpeed">1.0x</span>
                                </button>

                                <!-- Center Controls: Rewind, Play/Pause, Forward -->
                                <div class="flex items-center gap-3">
                                    <!-- Rewind 15s -->
                                    <button 
                                        type="button" 
                                        class="h-8 w-8 rounded-full text-slate-300 hover:text-white hover:bg-white/10 flex items-center justify-center transition cursor-pointer"
                                        title="-15 सेकंड"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>

                                    <!-- Main Play/Pause Button -->
                                    <button 
                                        type="button" 
                                        @click="togglePlay()" 
                                        class="h-12 w-12 rounded-full bg-gradient-to-tr from-orange-600 to-amber-500 text-white flex items-center justify-center shadow-lg hover:scale-105 active:scale-95 transition cursor-pointer"
                                        :title="isPlaying ? 'Pause' : 'Play'"
                                    >
                                        <template x-if="!isPlaying">
                                            <svg class="h-5 w-5 fill-current translate-x-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </template>
                                        <template x-if="isPlaying">
                                            <svg class="h-5 w-5 fill-current" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                                        </template>
                                    </button>

                                    <!-- Forward 15s -->
                                    <button 
                                        type="button" 
                                        class="h-8 w-8 rounded-full text-slate-300 hover:text-white hover:bg-white/10 flex items-center justify-center transition cursor-pointer"
                                        title="+15 सेकंड"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Volume / Status -->
                                <div class="text-[11px] text-slate-400 flex items-center gap-1">
                                    <span class="h-2 w-2 rounded-full" :class="isPlaying ? 'bg-emerald-400 animate-ping' : 'bg-slate-500'"></span>
                                    <span x-text="isPlaying ? 'Playing' : 'Ready'">Ready</span>
                                </div>
                            </div>
                        </div>

                        <!-- Playlist Items -->
                        <div class="space-y-2">
                            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">सुझाए गए ऑडियो पाठ</p>
                            <div class="space-y-1.5">
                                @foreach ($audioBooks->take(4) as $aItem)
                                    @php
                                        $aiTitle = $aItem->localized()?->title ?? $aItem->slug;
                                        $aiAuthor = $aItem->authors->first()?->localized()?->name ?? 'पारंपरिक';
                                        $aiText = $aItem->localized()?->lyrics ?? $aItem->localized()?->description ?? $aiTitle;
                                    @endphp
                                    <button 
                                        type="button" 
                                        @click="selectTrack('{{ addslashes($aiTitle) }}', '{{ addslashes($aiAuthor) }}', '{{ $aItem->slug }}', '{{ $aItem->cover_url }}', '{{ addslashes(preg_replace('/\s+/', ' ', Str::limit($aiText, 800))) }}')"
                                        class="w-full text-left rounded-xl p-2.5 hover:bg-white/10 transition flex items-center justify-between group cursor-pointer border border-transparent hover:border-white/10"
                                    >
                                        <div class="flex items-center gap-3 min-w-0">
                                            <span class="h-7 w-7 rounded-lg bg-white/10 text-slate-300 group-hover:bg-orange-600 group-hover:text-white flex items-center justify-center text-xs shrink-0 transition">
                                                <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            </span>
                                            <div class="truncate">
                                                <p class="text-xs font-semibold text-slate-200 group-hover:text-white truncate">{{ $aiTitle }}</p>
                                                <p class="text-[10px] text-slate-400 truncate">{{ $aiAuthor }}</p>
                                            </div>
                                        </div>
                                        <span class="text-[10px] text-slate-400 shrink-0 font-mono">
                                            {{ $aItem->pages ? ($aItem->pages * 2).' मि.' : '09:45 मि.' }}
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 4: CHILDREN'S STORIES (बच्चों की कहानियाँ)               -->
    <!-- Mobile Kid Banner, Age Pills, Panchatantra, Akbar Birbal       -->
    <!-- ============================================================== -->
    @if ($childrenStories->isNotEmpty())
        <section id="children-stories-section" class="border-t border-stone-200/80 pt-10">
            <x-ui.container size="default">
                <!-- Section Header with Age Pills -->
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
                    <div>
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-bold text-emerald-900 mb-2">
                            <span>🧒 बाल साहित्य • Moral Tales for Young Readers</span>
                        </div>
                        <h2 class="text-2xl font-extrabold text-slate-900 font-serif">
                            बच्चों की कहानियाँ (Children's Stories)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            संस्कार, ज्ञान और मनोरंजन से भरपूर पंचतंत्र, अकबर-बीरबल व तेनालीराम की कथाएँ
                        </p>
                    </div>

                    <!-- Age Filter Pills -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar text-xs font-semibold">
                        <button 
                            type="button" 
                            @click="childrenAge = 'all'" 
                            :class="childrenAge === 'all' ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 border border-stone-200 hover:bg-stone-50'"
                            class="px-3 py-1 rounded-xl transition cursor-pointer"
                        >
                            सभी उम्र
                        </button>
                        <button 
                            type="button" 
                            @click="childrenAge = '3-5'" 
                            :class="childrenAge === '3-5' ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 border border-stone-200 hover:bg-stone-50'"
                            class="px-3 py-1 rounded-xl transition cursor-pointer"
                        >
                            3-5 वर्ष
                        </button>
                        <button 
                            type="button" 
                            @click="childrenAge = '6-8'" 
                            :class="childrenAge === '6-8' ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 border border-stone-200 hover:bg-stone-50'"
                            class="px-3 py-1 rounded-xl transition cursor-pointer"
                        >
                            6-8 वर्ष
                        </button>
                        <button 
                            type="button" 
                            @click="childrenAge = '9-12'" 
                            :class="childrenAge === '9-12' ? 'bg-emerald-700 text-white' : 'bg-white text-slate-600 border border-stone-200 hover:bg-stone-50'"
                            class="px-3 py-1 rounded-xl transition cursor-pointer"
                        >
                            9-12 वर्ष
                        </button>
                    </div>
                </div>

                <!-- Kid Special Banner (Matching Mobile Reference media_1789461360426.jpg) -->
                <div class="rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white p-5 sm:p-6 mb-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-2xl bg-white/20 text-white flex items-center justify-center text-2xl shrink-0">
                            ✨
                        </div>
                        <div>
                            <h3 class="text-base font-bold font-serif leading-tight">बच्चों के लिए विशेष साहित्य संग्रह</h3>
                            <p class="text-xs text-emerald-100 mt-1 max-w-xl leading-relaxed">
                                आसान भाषा, सुंदर चित्रों व जीवन मूल्यों से भरपूर कहानियाँ जो बच्चों में पढ़ने की आदत विकसित करती हैं।
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('categories.show', 'children-stories') }}" class="rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 px-4 py-2 text-xs font-bold shadow-xs transition shrink-0">
                        सम्पूर्ण बाल संग्रह &rarr;
                    </a>
                </div>

                <!-- Children Stories Cards Grid (4 Cards) -->
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    @foreach ($childrenStories->take(4) as $cStory)
                        @php
                            $cTitle = $cStory->localized()?->title ?? $cStory->slug;
                            $cShort = $cStory->localized()?->short_description ?? Str::limit(strip_tags($cStory->localized()?->description ?? ''), 80);
                        @endphp
                        <div class="group rounded-2xl border border-stone-200 bg-white p-3.5 sm:p-4 shadow-2xs hover:shadow-md hover:border-emerald-300 transition flex flex-col justify-between">
                            <div class="relative w-full aspect-[2/3] overflow-hidden rounded-xl bg-slate-900 shadow-inner mb-3">
                                @if ($cStory->cover_url)
                                    <img src="{{ $cStory->cover_url }}" alt="{{ $cTitle }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy" />
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-emerald-950 via-teal-900 to-slate-900 p-3 text-center text-white flex flex-col justify-between">
                                        <span class="text-[8px] uppercase tracking-widest text-emerald-300 font-bold">BAL SAHITYA</span>
                                        <span class="font-serif text-xs font-bold line-clamp-3">{{ $cTitle }}</span>
                                        <span class="text-[8px] text-emerald-200">पंचतंत्र व नीति</span>
                                    </div>
                                @endif
                                <span class="absolute top-2 left-2 inline-flex items-center rounded-md bg-emerald-700/90 text-white px-2 py-0.5 text-[9px] font-bold">
                                    बाल कथा
                                </span>
                            </div>

                            <div>
                                <h3 class="font-bold text-sm text-slate-900 font-serif group-hover:text-emerald-800 transition line-clamp-1 leading-snug">
                                    <a href="{{ route('books.show', $cStory->slug) }}">
                                        {{ $cTitle }}
                                    </a>
                                </h3>
                                @if ($cShort)
                                    <p class="mt-1 text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                        {{ $cShort }}
                                    </p>
                                @endif
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between">
                                <a href="{{ route('books.show', $cStory->slug) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 transition">
                                    कहानी पढ़ें &rarr;
                                </a>
                                <a href="{{ route('books.interactive_reader', $cStory->slug) }}" class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 px-2 py-1 text-[10px] font-semibold text-emerald-800 transition" title="3D Flip Reader">
                                    📖 3D रीडर
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.container>
        </section>
    @endif

    <!-- ============================================================== -->
    <!-- SECTION 5: CATEGORIES DISCOVERY (Explore By Category)          -->
    <!-- 8 Clean Outline Cards with Database Book Counts                -->
    <!-- ============================================================== -->
    <section id="browse-categories" class="border-t border-stone-200/80 pt-10">
        <x-ui.container size="default">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-0.5 text-xs font-bold text-orange-900 mb-2">
                        <span>🏷️ विषयवार वर्गीकरण • Curated Categories</span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 font-serif">
                        Explore By Category (प्रमुख श्रेणियाँ)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        अपनी रुचि के अनुसार विभिन्न विधाओं में पुस्तकें खोजें
                    </p>
                </div>

                <a href="{{ route('categories.index') }}" class="text-xs font-bold text-orange-700 hover:text-orange-900 transition flex items-center gap-1 shrink-0">
                    <span>सभी श्रेणियाँ</span>
                    <span>&rarr;</span>
                </a>
            </div>

            @if ($categories->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($categories as $cat)
                        @php
                            $catName = $cat->translations->where('locale', 'hi')->first()?->name 
                                ?? $cat->translations->where('locale', 'en')->first()?->name 
                                ?? $cat->slug;
                        @endphp
                        <a href="{{ route('categories.show', $cat->slug) }}" class="group rounded-2xl border border-stone-200 bg-white p-4 shadow-2xs hover:shadow-md hover:border-orange-300 transition flex items-center gap-3.5">
                            <div class="h-11 w-11 shrink-0 rounded-xl bg-orange-50 text-orange-600 group-hover:bg-orange-600 group-hover:text-white flex items-center justify-center transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-slate-900 group-hover:text-orange-700 transition truncate">
                                    {{ $catName }}
                                </h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $cat->books_count ?? 0 }} पुस्तकें
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
                    <p class="text-xs text-slate-500">Explore By Category entries are being compiled.</p>
                </div>
            @endif
        </x-ui.container>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 6: DISTINGUISHED AUTHORS (प्रसिद्ध लेखक)               -->
    <!-- 6 Circular Portraits with Hindi Titles & Book Counts           -->
    <!-- ============================================================== -->
    <section id="browse-authors" class="border-t border-stone-200/80 pt-10">
        <x-ui.container size="default">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-stone-100 px-3 py-0.5 text-xs font-bold text-stone-800 mb-2">
                        <span>✒️ अमर रचनाकार • Distinguished Authors</span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 font-serif">
                        Distinguished Authors (प्रसिद्ध लेखक)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        हिंदी साहित्य के महान स्तंभों की अमर कृतियों का संग्रह
                    </p>
                </div>

                <a href="{{ route('authors.index') }}" class="text-xs font-bold text-orange-700 hover:text-orange-900 transition flex items-center gap-1 shrink-0">
                    <span>सभी लेखक</span>
                    <span>&rarr;</span>
                </a>
            </div>

            @if ($authors->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-5">
                    @foreach ($authors as $author)
                        @php
                            $aName = $author->translations->where('locale', 'hi')->first()?->name 
                                ?? $author->translations->where('locale', 'en')->first()?->name 
                                ?? $author->slug;
                            $initials = collect(explode(' ', $aName))->map(fn($s) => mb_substr($s, 0, 1))->take(2)->join('');
                        @endphp
                        <a href="{{ route('authors.show', $author->slug) }}" class="group rounded-2xl border border-stone-200 bg-white p-4 text-center shadow-2xs hover:shadow-md hover:border-orange-300 transition flex flex-col items-center justify-between">
                            <div class="relative h-20 w-20 rounded-full overflow-hidden bg-stone-100 border-2 border-orange-200 group-hover:border-orange-500 transition shadow-inner flex items-center justify-center text-orange-800 font-bold text-lg mb-3">
                                @if ($author->photo_url)
                                    <img src="{{ $author->photo_url }}" alt="{{ $aName }}" class="h-full w-full object-cover group-hover:scale-105 transition duration-300" loading="lazy" />
                                @else
                                    <span>{{ $initials }}</span>
                                @endif
                            </div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-orange-700 transition line-clamp-1">
                                {{ $aName }}
                            </h3>
                            <span class="mt-2 inline-flex items-center rounded-full bg-stone-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">
                                {{ $author->books_count ?? 0 }} पुस्तकें
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center">
                    <p class="text-xs text-slate-500">Distinguished Authors entries are being compiled.</p>
                </div>
            @endif
        </x-ui.container>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 7: POPULAR BOOKS (सर्वाधिक लोकप्रिय पुस्तकें)          -->
    <!-- Horizontal Rail with Smooth Navigation                         -->
    <!-- ============================================================== -->
    @if ($popularBooks->isNotEmpty())
        <section id="popular-books" class="border-t border-stone-200/80 pt-10" x-data="{
            scrollLeft() { $refs.rail.scrollBy({ left: -320, behavior: 'smooth' }); },
            scrollRight() { $refs.rail.scrollBy({ left: 320, behavior: 'smooth' }); }
        }">
            <x-ui.container size="default">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-0.5 text-xs font-bold text-orange-900 mb-2">
                            <span>🔥 पाठकों की पसंद • Most Popular</span>
                        </div>
                        <h2 class="text-2xl font-extrabold text-slate-900 font-serif">
                            सर्वाधिक लोकप्रिय पुस्तकें (Popular This Week)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            इस सप्ताह सबसे अधिक पढ़ी और सराही गई पुस्तकें
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="scrollLeft()" 
                            class="h-8 w-8 rounded-full border border-stone-200 bg-white hover:bg-orange-50 hover:border-orange-300 text-slate-600 flex items-center justify-center transition shadow-2xs cursor-pointer" 
                            aria-label="Previous"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button 
                            type="button" 
                            @click="scrollRight()" 
                            class="h-8 w-8 rounded-full border border-stone-200 bg-white hover:bg-orange-50 hover:border-orange-300 text-slate-600 flex items-center justify-center transition shadow-2xs cursor-pointer" 
                            aria-label="Next"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <a href="{{ route('books.index', ['sort' => 'popular']) }}" class="hidden sm:inline-flex text-xs font-bold text-orange-700 hover:text-orange-900 transition ml-2">
                            सभी देखें &rarr;
                        </a>
                    </div>
                </div>

                <div x-ref="rail" class="flex gap-4 overflow-x-auto pb-4 pt-1 no-scrollbar snap-x snap-mandatory">
                    @foreach ($popularBooks as $pBook)
                        <div class="w-44 sm:w-48 shrink-0 snap-start">
                            <x-book-card :book="$pBook" />
                        </div>
                    @endforeach
                </div>
            </x-ui.container>
        </section>
    @endif

    <!-- ============================================================== -->
    <!-- SECTION 8: MULTI-DEVICE PROMOTIONAL BANNER                     -->
    <!-- "कहीं भी पढ़ें, कहीं भी सुनें" (Kindle/Audible Style Dark Navy)-->
    <!-- ============================================================== -->
    <section id="multi-device-banner" class="border-t border-stone-200/80 pt-10">
        <x-ui.container size="default">
            <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-slate-900 to-stone-900 text-white p-8 sm:p-12 shadow-xl border border-slate-800">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Left Copy & CTAs -->
                    <div class="lg:col-span-7 space-y-5 text-center lg:text-left">
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-orange-500/20 px-3 py-0.5 text-xs font-bold text-orange-300 border border-orange-500/30">
                            <span>📱 सम्पूर्ण डिजिटल सुलभता</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold font-serif leading-tight">
                            कहीं भी पढ़ें, कहीं भी सुनें
                        </h2>
                        <p class="text-sm text-slate-300 leading-relaxed max-w-xl mx-auto lg:mx-0">
                            चाहे आपके पास स्मार्टफोन हो, टैबलेट या कंप्यूटर — हिंदी पीडीएफ लाइब्रेरी पर आपका अध्ययन कभी नहीं रुकता। आधुनिक 3D फ्लिप रीडर और ऑडियो प्लेयर के साथ बिना किसी ऐप इंस्टॉलेशन के तुरंत पढ़ें।
                        </p>
                        
                        <div class="space-y-2 text-xs text-slate-300 text-left max-w-md mx-auto lg:mx-0">
                            <p class="flex items-center gap-2">
                                <span class="text-emerald-400 font-bold">✓</span>
                                <span>100% निःशुल्क और विज्ञापन-मुक्त अनुभव</span>
                            </p>
                            <p class="flex items-center gap-2">
                                <span class="text-emerald-400 font-bold">✓</span>
                                <span>ऑफलाइन अध्ययन हेतु सुरक्षित डायरेक्ट PDF डाउनलोड</span>
                            </p>
                            <p class="flex items-center gap-2">
                                <span class="text-emerald-400 font-bold">✓</span>
                                <span>पेज-बाय-पेज शुद्ध हिंदी टेक्स्ट और ऑडियो वाचन</span>
                            </p>
                        </div>

                        <div class="pt-2 flex flex-wrap items-center justify-center lg:justify-start gap-3">
                            <a href="{{ route('books.index') }}" class="rounded-xl bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-700 hover:to-amber-700 text-white px-5 py-2.5 text-xs font-bold shadow-sm transition cursor-pointer">
                                अभी पढ़ना शुरू करें
                            </a>
                            <a href="#audiobooks-hub" class="rounded-xl border border-slate-700 hover:bg-white/10 text-slate-200 px-5 py-2.5 text-xs font-bold transition cursor-pointer">
                                ऑडियो पुस्तकें देखें
                            </a>
                        </div>
                    </div>

                    <!-- Right Multi-Device Mockup Graphic -->
                    <div class="lg:col-span-5 flex justify-center items-center">
                        <div class="relative w-full max-w-sm aspect-video sm:aspect-4/3 rounded-2xl bg-white/5 border border-white/10 p-4 flex flex-col justify-between shadow-2xl backdrop-blur-xs">
                            <div class="flex items-center justify-between border-b border-white/10 pb-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-mono">hindipdflibrary.com</span>
                            </div>
                            <div class="py-4 text-center space-y-2">
                                <p class="text-xs text-orange-400 font-serif">श्रीमद्भगवद्गीता • अध्याय १</p>
                                <p class="text-sm font-bold text-white font-serif leading-relaxed">
                                    धर्मक्षेत्रे कुरुक्षेत्रे समवेता युयुत्सवः।<br>
                                    मामकाः पाण्डवाश्चैव किमकुर्वत सञ्जय॥
                                </p>
                                <div class="pt-2 flex items-center justify-center gap-1 text-[11px] text-slate-400">
                                    <span>🎧 3D Flip Reader • Audio Synced</span>
                                </div>
                            </div>
                            <div class="border-t border-white/10 pt-2 flex items-center justify-between text-[10px] text-slate-400">
                                <span>पृष्ठ १ / १८०</span>
                                <span class="text-emerald-400">● 100% Free Access</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 9: 5-COLUMN BENEFITS STRIP                             -->
    <!-- 100% Free, Online Reader, Audio, Download, Cultural Heritage   -->
    <!-- ============================================================== -->
    <section id="benefits-strip" class="border-t border-stone-200/80 pt-10">
        <x-ui.container size="default">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <!-- Benefit 1: Free -->
                <div class="rounded-2xl border border-stone-200 bg-white p-4 text-center shadow-2xs">
                    <div class="h-10 w-10 mx-auto rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mb-2.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-xs font-bold text-slate-900 font-serif">100% निःशुल्क एवं सुरक्षित</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">कोई छिपा शुल्क नहीं</p>
                </div>

                <!-- Benefit 2: Online Reader -->
                <div class="rounded-2xl border border-stone-200 bg-white p-4 text-center shadow-2xs">
                    <div class="h-10 w-10 mx-auto rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h4 class="text-xs font-bold text-slate-900 font-serif">इंटरैक्टिव 3D फ्लिप रीडर</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">ब्राउज़र में सीधे पढ़ें</p>
                </div>

                <!-- Benefit 3: Audio -->
                <div class="rounded-2xl border border-stone-200 bg-white p-4 text-center shadow-2xs">
                    <div class="h-10 w-10 mx-auto rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-2.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.757 3.63 8.25 4.51 8.25H6.75z" />
                        </svg>
                    </div>
                    <h4 class="text-xs font-bold text-slate-900 font-serif">इन-पेज ऑडियो वाचन</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">स्पष्ट हिंदी वाचन</p>
                </div>

                <!-- Benefit 4: Download -->
                <div class="rounded-2xl border border-stone-200 bg-white p-4 text-center shadow-2xs">
                    <div class="h-10 w-10 mx-auto rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-2.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                    </div>
                    <h4 class="text-xs font-bold text-slate-900 font-serif">तुरंत सुरक्षित PDF डाउनलोड</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">ऑफलाइन अध्ययन हेतु</p>
                </div>

                <!-- Benefit 5: Cultural Heritage -->
                <div class="col-span-2 sm:col-span-1 rounded-2xl border border-stone-200 bg-white p-4 text-center shadow-2xs">
                    <div class="h-10 w-10 mx-auto rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mb-2.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <h4 class="text-xs font-bold text-slate-900 font-serif">सांस्कृतिक धरोहर</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">भारतीय साहित्य संरक्षण</p>
                </div>
            </div>
        </x-ui.container>
    </section>

</div>
@endsection