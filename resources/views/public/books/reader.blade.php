<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $book->title }} — पाठक व ऑडियोबुक | {{ config('app.name', 'Hindi PDF Library') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Alpine.js (ensures state machine works on standalone page) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        html, body { height: 100%; overflow: hidden; }
        body { background: #0d1117; color: #e6edf3; font-family: system-ui, -apple-system, sans-serif; }

        .reader-root { display: flex; flex-direction: column; height: 100dvh; height: 100vh; }
        .reader-header { flex-shrink: 0; height: 56px; background: rgba(13,17,23,.97); border-bottom: 1px solid #21262d; backdrop-filter: blur(12px); }
        .reader-body   { flex: 1; overflow: hidden; display: flex; min-height: 0; }
        .reader-footer { flex-shrink: 0; height: 72px; background: rgba(13,17,23,.97); border-top: 1px solid #21262d; }

        .page-text {
            font-family: 'Noto Serif Devanagari', 'Mangal', Georgia, serif;
            font-size: clamp(1rem, 2vw, 1.25rem);
            line-height: 1.95;
            color: #e6edf3;
            text-align: justify;
            hyphens: auto;
        }
        .page-text.en { font-family: Georgia, serif; }

        .is-speaking { border-left: 3px solid #3b82f6; padding-left: 1rem; }

        @keyframes wave { 0%,100%{transform:scaleY(.3)} 50%{transform:scaleY(1)} }
        .bar { display:inline-block; width:3px; border-radius:2px; background:#3b82f6; animation:wave 1s ease-in-out infinite; }
        .bar:nth-child(2){animation-delay:.15s;}
        .bar:nth-child(3){animation-delay:.3s;}
        .bar:nth-child(4){animation-delay:.45s;}
        .bar:nth-child(5){animation-delay:.6s;}

        .progress-bar { height: 3px; background: #21262d; }
        .progress-fill { height: 3px; background: linear-gradient(90deg, #3b82f6, #6366f1); transition: width .3s; }

        .mode-tab { padding: .35rem .75rem; border-radius: 6px; font-size: .75rem; font-weight: 600; cursor: pointer; transition: all .15s; }
        .mode-tab.active  { background: #1f6feb; color: #fff; }
        .mode-tab.inactive{ background: transparent; color: #8b949e; }
        .mode-tab.inactive:hover { color: #e6edf3; background: #161b22; }

        .text-scroll {
            flex: 1; overflow-y: auto; padding: 2rem 1.5rem;
            scrollbar-width: thin; scrollbar-color: #30363d transparent;
        }
        .text-scroll::-webkit-scrollbar { width: 5px; }
        .text-scroll::-webkit-scrollbar-thumb { background: #30363d; border-radius: 3px; }

        @media (max-width: 640px) {
            .reader-header { height: 52px; }
            .reader-footer { height: 76px; }
            .text-scroll { padding: 1.25rem 1rem; }
            .page-text { font-size: 1rem; }
        }

        input[type=range] { -webkit-appearance:none; appearance:none; height:4px; border-radius:2px; background:#30363d; outline:none; }
        input[type=range]::-webkit-slider-thumb { -webkit-appearance:none; width:14px; height:14px; border-radius:50%; background:#3b82f6; cursor:pointer; }
        input[type=range]::-moz-range-thumb { width:14px; height:14px; border-radius:50%; background:#3b82f6; cursor:pointer; border:none; }
    </style>

    <script>
    function readerApp() {
        return {
            /* Static config injected from PHP */
            readerMode: '{{ $readerMode }}',
            isArchive:  {{ $isArchive ? 'true' : 'false' }},
            archiveId:  '{{ $archiveIdentifier ?? '' }}',
            pdfUrl:     '{{ $pdfUrl ?? '' }}',
            manifestUrl:'{{ route("books.archive_manifest", ["slug" => $book->slug]) }}',
            fallback:   @js($book->description ?: ($book->title . ' — हिन्दी साहित्य की उत्कृष्ट कृति।')),
            serverPages: @json($bookTextPages),

            /* Dynamic reactive state */
            mode:       '{{ $initialMode ?? "read" }}',
            pages:      [],
            page:       1,
            totalPages: 1,
            lang:       'hi',
            fontSize:   18,
            isPlaying:  false,
            isLoadingText: false,
            isTranslating: false,
            autoNext:   true,
            rate:       1.0,
            availableVoices: [],
            translationsCache: {},
            utterance:  null,

            get currentPageTitle() {
                const p = this.pages.find(p => p.page === this.page);
                return p?.title || ('पृष्ठ ' + this.page);
            },

            get currentText() {
                if (this.lang === 'en' && this.translationsCache[this.page]) {
                    return this.translationsCache[this.page];
                }
                const p = this.pages.find(p => p.page === this.page);
                return p ? p.text : this.fallback;
            },

            get progress() {
                if (this.totalPages < 1) return 0;
                return Math.round((this.page / this.totalPages) * 100);
            },

            /* Initialize state and speech */
            async init() {
                if (this.serverPages && this.serverPages.length > 0) {
                    this.pages = this.serverPages;
                    this.totalPages = this.pages.length;
                } else {
                    this.pages = [{
                        page: 1,
                        title: 'पुस्तक परिचय',
                        text: this.fallback
                    }];
                    this.totalPages = 1;
                }

                if ('speechSynthesis' in window) {
                    const getVoices = () => { 
                        this.availableVoices = window.speechSynthesis.getVoices(); 
                    };
                    getVoices();
                    window.speechSynthesis.onvoiceschanged = getVoices;
                }

                if (this.mode === 'listen') {
                    setTimeout(() => { this.playAudio(); }, 700);
                }

                document.addEventListener('keydown', (e) => {
                    if (e.target.tagName === 'INPUT') return;
                    if (e.key === 'ArrowRight') this.nextPage();
                    if (e.key === 'ArrowLeft')  this.prevPage();
                    if (e.key === ' ') { e.preventDefault(); this.toggleAudio(); }
                });
            },

            setMode(m) {
                if (m === 'listen') {
                    this.mode = 'listen';
                    this.playAudio();
                } else if (m === 'read') {
                    this.mode = 'read';
                } else if (m === 'pdf') {
                    this.stopAudio();
                    this.mode = 'pdf';
                }
            },

            prevPage() {
                if (this.page > 1) {
                    this.page--;
                    this.scrollToTop();
                    if (this.isPlaying) this.playAudio();
                }
            },

            nextPage() {
                if (this.page < this.totalPages) {
                    this.page++;
                    this.scrollToTop();
                    if (this.isPlaying) this.playAudio();
                } else {
                    this.stopAudio();
                }
            },

            onSlider() {
                this.scrollToTop();
                if (this.isPlaying) this.playAudio();
            },

            scrollToTop() {
                const el = this.$el.querySelector('.text-scroll');
                if (el) el.scrollTop = 0;
            },

            async setLang(l) {
                this.lang = l;
                if (l === 'en' && !this.translationsCache[this.page]) {
                    await this.translatePage();
                }
                if (this.isPlaying) this.playAudio();
            },

            async translatePage() {
                const original = this.currentText;
                this.isTranslating = true;
                try {
                    const sample = original.substring(0, 500);
                    const url = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(sample)}&langpair=hi|en`;
                    const r = await fetch(url);
                    if (r.ok) {
                        const d = await r.json();
                        if (d?.responseData?.translatedText) {
                            this.translationsCache[this.page] = d.responseData.translatedText;
                        }
                    }
                } catch(e) {
                    console.warn('Translation error', e);
                } finally {
                    this.isTranslating = false;
                }
            },

            toggleAudio() {
                if (this.isPlaying) {
                    this.stopAudio();
                } else {
                    if (this.mode === 'read') this.mode = 'listen';
                    this.playAudio();
                }
            },

            findVoice(isHindi) {
                const voices = this.availableVoices && this.availableVoices.length > 0 
                    ? this.availableVoices 
                    : window.speechSynthesis.getVoices();

                if (!voices || voices.length === 0) return null;

                if (isHindi) {
                    // Priority 1: High quality natural / online Hindi voice
                    let v = voices.find(voice => 
                        (voice.lang === 'hi-IN' || voice.lang === 'hi_IN' || voice.lang.toLowerCase().startsWith('hi')) &&
                        (voice.name.includes('Natural') || voice.name.includes('Online') || voice.name.includes('Google'))
                    );
                    if (v) return v;

                    // Priority 2: Any voice with hi-IN or hi* language tag
                    v = voices.find(voice => 
                        voice.lang === 'hi-IN' || voice.lang === 'hi_IN' || voice.lang.toLowerCase().startsWith('hi')
                    );
                    if (v) return v;

                    // Priority 3: Voice with Hindi / हिन्दी / Swara / Madhur / Hemant / Kalpana in name
                    v = voices.find(voice => {
                        const n = (voice.name || '').toLowerCase();
                        return n.includes('hindi') || n.includes('हिन्दी') || n.includes('swara') || n.includes('madhur') || n.includes('hemant') || n.includes('kalpana');
                    });
                    if (v) return v;

                    // CRITICAL: Return null so browser's native hi-IN engine is used instead of forcing an English voice
                    return null;
                } else {
                    return voices.find(v => v.lang.startsWith('en') && (v.name.includes('Natural') || v.name.includes('Online') || v.name.includes('Google')))
                        || voices.find(v => v.lang.startsWith('en'))
                        || null;
                }
            },

            playAudio() {
                if (!('speechSynthesis' in window)) {
                    alert('Audio speech synthesis requires Chrome, Safari, or Edge.');
                    return;
                }

                window.speechSynthesis.cancel();

                setTimeout(() => {
                    const text = this.currentText;
                    if (!text || text.trim().length < 2) {
                        this.isPlaying = false;
                        return;
                    }

                    const clean = text.replace(/[*#_`~[\]()]/g, ' ').replace(/\s+/g, ' ').trim();
                    const u = new SpeechSynthesisUtterance(clean);
                    u.rate = this.rate || 1.0;

                    const isHindi = /[\u0900-\u097F]/.test(clean) || this.lang === 'hi';
                    u.lang = isHindi ? 'hi-IN' : 'en-US';

                    const matchedVoice = this.findVoice(isHindi);
                    if (matchedVoice) {
                        u.voice = matchedVoice;
                    }

                    u.onstart = () => { this.isPlaying = true; };
                    u.onend = () => {
                        this.isPlaying = false;
                        if (this.autoNext && this.page < this.totalPages) {
                            setTimeout(() => { this.nextPage(); }, 600);
                        }
                    };
                    u.onerror = (e) => {
                        console.warn('TTS error', e);
                        if (e.error !== 'interrupted') this.isPlaying = false;
                    };

                    this.utterance = u;
                    window.speechSynthesis.speak(u);
                    this.isPlaying = true;
                }, 60);
            },

            stopAudio() {
                if ('speechSynthesis' in window) {
                    window.speechSynthesis.cancel();
                }
                this.isPlaying = false;
            },

            setRate(r) {
                this.rate = r;
                if (this.isPlaying) this.playAudio();
            },

            toggleFS() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(() => {});
                } else {
                    document.exitFullscreen?.().catch(() => {});
                }
            }
        };
    }
    </script>
</head>
<body class="reader-root" x-data="readerApp()" x-init="init()">

    {{-- ═══════════ HEADER ═══════════ --}}
    <header class="reader-header flex items-center px-3 sm:px-5 gap-3 z-30">
        {{-- Back button --}}
        <a href="{{ route('books.show', $book->slug) }}"
           class="shrink-0 flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white transition px-2.5 py-1.5 rounded-lg hover:bg-white/5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            <span class="hidden sm:inline">Back</span>
        </a>

        {{-- Book title --}}
        <div class="flex-1 min-w-0">
            <div class="text-sm font-bold text-white truncate">{{ $book->title }}</div>
            <div class="text-[11px] text-blue-400 truncate">{{ $book->authors->first()?->name ?? '' }}</div>
        </div>

        {{-- Mode switcher tabs --}}
        <div class="flex items-center gap-1 shrink-0">
            <button type="button" @click="setMode('read')"
                :class="mode==='read' ? 'mode-tab active' : 'mode-tab inactive'">
                📖 पढ़ें (Read)
            </button>
            <button type="button" @click="setMode('listen')"
                :class="mode==='listen' ? 'mode-tab active' : 'mode-tab inactive'">
                🎧 सुनें (Audiobook)
            </button>
            @if($pdfUrl)
            <button type="button" @click="setMode('pdf')"
                :class="mode==='pdf' ? 'mode-tab active' : 'mode-tab inactive'"
                class="hidden sm:block">
                📄 मूल PDF
            </button>
            @elseif($isArchive && $archiveDetailsUrl)
            <a href="{{ $archiveDetailsUrl }}" target="_blank" rel="noopener"
               class="mode-tab inactive hidden sm:block">
                🌐 Archive.org
            </a>
            @endif
        </div>

        {{-- Fullscreen --}}
        <button type="button" @click="toggleFS()"
            class="shrink-0 p-1.5 rounded-lg text-slate-500 hover:text-white hover:bg-white/5 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/>
            </svg>
        </button>
    </header>

    {{-- Progress bar --}}
    <div class="progress-bar shrink-0">
        <div class="progress-fill" :style="'width:'+progress+'%'"></div>
    </div>

    {{-- ═══════════ BODY ═══════════ --}}
    <div class="reader-body">

        {{-- ─── TEXT & AUDIOBOOK VIEW (Default, always visible, never black) ─── --}}
        <div class="flex-1 flex flex-col min-h-0 max-w-3xl mx-auto w-full"
             x-show="mode === 'read' || mode === 'listen'">

            {{-- Speaking indicator (listen mode) --}}
            <div x-show="mode==='listen' && isPlaying"
                 class="shrink-0 flex items-center gap-3 px-5 py-2.5 bg-blue-950/50 border-b border-blue-800/30">
                <div class="flex items-end gap-0.5 h-5">
                    <span class="bar h-2"></span><span class="bar h-4"></span>
                    <span class="bar h-3"></span><span class="bar h-5"></span><span class="bar h-2"></span>
                </div>
                <span class="text-xs font-semibold text-blue-300">
                    ऑडियो वाचन चालू है — पृष्ठ <span x-text="page">1</span>
                    <span x-show="lang==='en'"> (English)</span>
                </span>
                <button type="button" @click="stopAudio()" class="ml-auto text-xs text-blue-400 hover:text-white transition">⏹ बंद करें (Stop)</button>
            </div>

            {{-- Page text content container --}}
            <div class="text-scroll">
                <div>
                    {{-- Page header bar --}}
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/10">
                        <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider flex items-center gap-2">
                            <span>पृष्ठ <span class="text-blue-400" x-text="page">1</span> / <span x-text="totalPages">{{ $totalTextPages ?: 1 }}</span></span>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- Language toggle --}}
                            <div class="flex rounded-lg overflow-hidden border border-white/10 text-[11px] font-bold">
                                <button type="button" @click="setLang('hi')"
                                    class="px-2.5 py-1 transition"
                                    :class="lang==='hi'?'bg-blue-600 text-white':'text-slate-500 hover:text-white'">
                                    हिंदी
                                </button>
                                <button type="button" @click="setLang('en')"
                                    class="px-2.5 py-1 transition"
                                    :class="lang==='en'?'bg-blue-600 text-white':'text-slate-500 hover:text-white'">
                                    English
                                </button>
                            </div>
                            {{-- Font size controls --}}
                            <button type="button" @click="fontSize = Math.min(fontSize+2, 28)"
                                class="text-slate-500 hover:text-white text-base leading-none px-1.5 py-0.5 rounded hover:bg-white/5 transition" title="बड़ा फ़ॉन्ट">A+</button>
                            <button type="button" @click="fontSize = Math.max(fontSize-2, 14)"
                                class="text-slate-500 hover:text-white text-xs leading-none px-1.5 py-0.5 rounded hover:bg-white/5 transition" title="छोटा फ़ॉन्ट">A−</button>
                        </div>
                    </div>

                    {{-- Page Title Heading --}}
                    <h2 class="text-base sm:text-lg font-bold text-blue-300 mb-4 font-serif" x-text="currentPageTitle">
                        {{ $bookTextPages[0]['title'] ?? 'पुस्तक परिचय' }}
                    </h2>

                    {{-- Main Page Text (Server pre-rendered fallback so it NEVER stays black!) --}}
                    <p class="page-text whitespace-pre-line"
                       :class="[lang==='en'?'en':'', isPlaying && mode==='listen'?'is-speaking':'']"
                       :style="'font-size:'+fontSize+'px'"
                       x-text="currentText">
                        {{ $bookTextPages[0]['text'] ?? ($translation?->description ?? $book->title) }}
                    </p>

                    <div x-show="isTranslating" class="flex items-center gap-2 mt-4 text-blue-400 text-xs">
                        <div class="h-3.5 w-3.5 border-2 border-blue-400 border-t-transparent rounded-full animate-spin"></div>
                        <span>अनुवाद हो रहा है…</span>
                    </div>

                    {{-- Bottom Navigation inside text --}}
                    <div class="flex items-center justify-between mt-8 pt-4 border-t border-white/10">
                        <button type="button" @click="prevPage()"
                            :disabled="page <= 1"
                            class="flex items-center gap-1.5 text-sm font-semibold text-slate-400 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed transition">
                            ← पिछला
                        </button>
                        <span class="text-xs text-slate-600" x-text="page + ' / ' + totalPages">
                            1 / {{ $totalTextPages ?: 1 }}
                        </span>
                        <button type="button" @click="nextPage()"
                            :disabled="page >= totalPages"
                            class="flex items-center gap-1.5 text-sm font-semibold text-slate-400 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed transition">
                            अगला →
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── EMBEDDED PDF VIEW (When user clicks "मूल PDF") ─── --}}
        @if($pdfUrl)
        <div class="flex-1 flex flex-col min-h-0 w-full" x-show="mode === 'pdf'">
            <div class="shrink-0 flex items-center justify-between px-4 py-2 bg-slate-900 border-b border-slate-800 text-xs">
                <span class="text-slate-300">📄 मूल PDF दस्तावेज़</span>
                <a href="{{ $pdfUrl }}" target="_blank" class="text-blue-400 hover:text-white font-semibold">नये टैब में खोलें →</a>
            </div>
            <iframe class="w-full flex-1 border-none bg-slate-950" src="{{ $pdfUrl }}"></iframe>
        </div>
        @endif

    </div>

    {{-- ═══════════ FOOTER AUDIO DOCK ═══════════ --}}
    <footer class="reader-footer flex items-center px-3 sm:px-5 gap-3 sm:gap-5 z-30"
            x-show="mode !== 'pdf'">

        {{-- Page scrubber --}}
        <div class="flex items-center gap-2 flex-1 min-w-0">
            <span class="text-xs font-bold text-slate-400 shrink-0">
                <span class="text-blue-400" x-text="page">1</span> / <span x-text="totalPages">{{ $totalTextPages ?: 1 }}</span>
            </span>
            <input type="range" min="1" :max="totalPages || 1" x-model.number="page"
                @input="onSlider()" x-show="totalPages > 1"
                class="flex-1 max-w-32 sm:max-w-48 cursor-pointer">
        </div>

        {{-- Center Audio Controls --}}
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            {{-- Prev page --}}
            <button type="button" @click="prevPage()" :disabled="page <= 1"
                class="p-2 text-slate-400 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed transition"
                title="पिछला पृष्ठ">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </button>

            {{-- Play / Pause Button --}}
            <button type="button" @click="toggleAudio()"
                class="relative h-12 w-12 rounded-full flex items-center justify-center text-white shadow-lg transition hover:scale-105 active:scale-95 cursor-pointer"
                :class="isPlaying ? 'bg-red-600 hover:bg-red-500 shadow-red-500/20' : 'bg-blue-600 hover:bg-blue-500 shadow-blue-500/20'"
                title="ऑडियो सुनें / रोकें">
                {{-- Play icon --}}
                <svg x-show="!isPlaying" class="h-5 w-5 fill-current ml-0.5" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                {{-- Pause icon --}}
                <svg x-show="isPlaying" class="h-5 w-5 fill-current" viewBox="0 0 24 24" style="display:none">
                    <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                </svg>
            </button>

            {{-- Next page --}}
            <button type="button" @click="nextPage()" :disabled="page >= totalPages"
                class="p-2 text-slate-400 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed transition"
                title="अगला पृष्ठ">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </button>
        </div>

        {{-- Right: Speed & Auto Next --}}
        <div class="flex items-center gap-2 justify-end flex-1">
            {{-- Auto-advance --}}
            <button type="button" @click="autoNext = !autoNext"
                class="hidden sm:flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full border transition cursor-pointer"
                :class="autoNext ? 'border-emerald-500/40 bg-emerald-950/50 text-emerald-300' : 'border-white/10 text-slate-500 hover:text-white'">
                <span class="h-1.5 w-1.5 rounded-full" :class="autoNext ? 'bg-emerald-400 animate-pulse' : 'bg-slate-600'"></span>
                Auto
            </button>

            {{-- Playback Speed selector --}}
            <div class="hidden sm:flex items-center gap-0.5 bg-white/5 p-0.5 rounded-full text-[11px] font-bold border border-white/10">
                <button type="button" @click="setRate(0.8)" class="px-2 py-0.5 rounded-full transition cursor-pointer"
                    :class="rate===0.8?'bg-blue-600 text-white':'text-slate-400 hover:text-white'">0.8×</button>
                <button type="button" @click="setRate(1.0)" class="px-2 py-0.5 rounded-full transition cursor-pointer"
                    :class="rate===1.0?'bg-blue-600 text-white':'text-slate-400 hover:text-white'">1×</button>
                <button type="button" @click="setRate(1.2)" class="px-2 py-0.5 rounded-full transition cursor-pointer"
                    :class="rate===1.2?'bg-blue-600 text-white':'text-slate-400 hover:text-white'">1.2×</button>
                <button type="button" @click="setRate(1.5)" class="px-2 py-0.5 rounded-full transition cursor-pointer"
                    :class="rate===1.5?'bg-blue-600 text-white':'text-slate-400 hover:text-white'">1.5×</button>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
