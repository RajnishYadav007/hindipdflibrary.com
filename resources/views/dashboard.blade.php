<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100/90 px-3 py-0.5 text-xs font-semibold text-amber-950 border border-amber-300">
                        <x-app-icon :name="$user->level_config['icon'] ?? 'sparkles'" class="w-3.5 h-3.5 text-amber-700" />
                        <span>{{ $user->level_config['label'] }}</span>
                    </span>
                    <button 
                        type="button" 
                        @click="currentTab = 'rewards'"
                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 hover:bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800 border border-emerald-200 transition cursor-pointer"
                    >
                        <x-app-icon name="trophy" class="w-3.5 h-3.5 text-emerald-600" />
                        <span>{{ number_format($user->total_points) }} लाइब्रेरी अंक</span>
                    </button>
                    <span class="text-xs text-slate-400">
                        सदस्यता: {{ $user->created_at->format('M Y') }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 font-serif">
                    नमस्ते, {{ $user->public_display_name }}
                </h1>
                <p class="mt-0.5 text-xs sm:text-sm text-slate-500">
                    डिजिटल हिन्दी पुस्तकालय पाठक एवं योगदानकर्ता डैशबोर्ड
                </p>
            </div>

            <!-- Header Quick Action: Universal Contribute Dropdown -->
            <div class="flex flex-wrap items-center gap-2.5 self-start sm:self-auto">
                <div class="relative" x-data="{ openContribute: false }">
                    <button 
                        type="button" 
                        @click="openContribute = !openContribute"
                        @click.away="openContribute = false"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 active:bg-amber-800 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>नया साहित्य जोड़ें</span>
                        <svg class="w-3.5 h-3.5 transition-transform" :class="{'rotate-180': openContribute}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Universal Dropdown Menu -->
                    <div 
                        x-show="openContribute" 
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        style="display: none;" 
                        class="absolute right-0 mt-2 w-64 rounded-2xl bg-white p-2 shadow-xl border border-slate-200 z-50 text-xs divide-y divide-slate-100"
                    >
                        <div class="p-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            साहित्य प्रारूप चुनें
                        </div>
                        <div class="py-1 space-y-0.5">
                            <a href="{{ route('dashboard.books.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="book-open" class="h-4 w-4 text-amber-700 shrink-0" />
                                <div>
                                    <div class="font-bold text-slate-900">नई पुस्तक (PDF Book)</div>
                                    <div class="text-[11px] text-slate-400">सार्वजनिक डोमेन या पाण्डुलिपि</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.submissions.audiobook.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="headphones" class="h-4 w-4 text-amber-700 shrink-0" />
                                <div>
                                    <div class="font-bold text-slate-900">ऑडियोबुक (Audiobook)</div>
                                    <div class="text-[11px] text-slate-400">स्वर वाचन व अध्याय ऑडियो</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.submissions.chalisa.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="flame" class="h-4 w-4 text-amber-700 shrink-0" />
                                <div>
                                    <div class="font-bold text-slate-900">श्री चालीसा (Chalisa)</div>
                                    <div class="text-[11px] text-slate-400">चालीसा स्तुति व दोहे</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.submissions.aarti.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="sparkles" class="h-4 w-4 text-amber-700 shrink-0" />
                                <div>
                                    <div class="font-bold text-slate-900">पावन आरती (Aarti)</div>
                                    <div class="text-[11px] text-slate-400">देव आरतियां व भजन पद</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.submissions.story.create', ['type' => 'children']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="sparkles" class="h-4 w-4 text-emerald-600 shrink-0" />
                                <div>
                                    <div class="font-bold text-slate-900">बाल कथा (Children Story)</div>
                                    <div class="text-[11px] text-slate-400">पंचतंत्र, हितोपदेश, बाल प्रसंग</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.submissions.story.create', ['type' => 'general']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="scroll-text" class="h-4 w-4 text-amber-700 shrink-0" />
                                <div>
                                    <div class="font-bold text-slate-900">अन्य / लोक कथा (General Story)</div>
                                    <div class="text-[11px] text-slate-400">पौराणिक, लोक कथाएं, प्रेरक आख्यान</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <a 
                    href="{{ route('books.index') }}" 
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-800 hover:bg-slate-50 shadow-2xs transition"
                >
                    <span>पुस्तकालय देखें (Browse)</span>
                </a>

                @if ($user->is_admin || $user->hasAnyRole(['Super Admin', 'Admin', 'Editor', 'Moderator']))
                    <a 
                        href="{{ url('/admin') }}" 
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-2.5 text-xs font-bold text-white hover:bg-slate-800 shadow-2xs transition"
                    >
                        <span>एडमिन पोर्टल &rarr;</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ 
        currentTab: '{{ $activeTab }}', 
        subFilter: 'all',
        openLedgerModal: false,
        openShareModal: false,
        shareUrl: '',
        shareTitle: '',
        copySuccess: false,
        openShare(url, title) {
            this.shareUrl = url;
            this.shareTitle = title;
            this.openShareModal = true;
            this.copySuccess = false;
        },
        copyText(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text);
            } else {
                let textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
            }
            this.copySuccess = true;
            setTimeout(() => this.copySuccess = false, 2500);
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Flash Notifications -->
            @if (session('status') || session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-xs font-semibold text-emerald-900 flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('status') ?? session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 text-base cursor-pointer">&times;</button>
                </div>
            @endif

            <!-- 1. Executive 4-Card Contribution KPI Summary -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- KPI 1: Total Contributions -->
                <button 
                    type="button" 
                    @click="currentTab = 'submissions'; subFilter = 'all'"
                    class="group text-left rounded-2xl border bg-white p-5 shadow-2xs transition hover:shadow-xs focus:outline-none cursor-pointer"
                    :class="currentTab === 'submissions' && subFilter === 'all' ? 'border-amber-600 ring-2 ring-amber-600/20' : 'border-slate-200/90 hover:border-amber-500'"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">कुल प्रस्तुतियां</span>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700 border border-amber-200 group-hover:bg-amber-600 group-hover:text-white transition">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-serif">{{ $stats['total_contributions'] }}</span>
                        <span class="text-xs text-slate-400">Submissions</span>
                    </div>
                    <div class="mt-1 text-[11px] text-slate-400 truncate">
                        {{ $stats['books_count'] }} बुक्स • {{ $stats['audiobooks_count'] }} ऑडियो • {{ $stats['devotionals_count'] }} चालीसा • {{ $stats['stories_count'] }} कथा
                    </div>
                </button>

                <!-- KPI 2: Under Review / Pending -->
                <button 
                    type="button" 
                    @click="currentTab = 'submissions'"
                    class="group text-left rounded-2xl border bg-white p-5 shadow-2xs transition hover:shadow-xs focus:outline-none cursor-pointer border-slate-200/90 hover:border-amber-500"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-700">समीक्षाधीन</span>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-200 group-hover:bg-amber-500 group-hover:text-white transition">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-extrabold text-amber-700 font-serif">{{ $stats['pending_count'] }}</span>
                        <span class="text-xs text-slate-400">In Review</span>
                    </div>
                    <div class="mt-1 text-[11px] text-amber-600/80">
                        संपादक समीक्षा प्रक्रिया जारी है
                    </div>
                </button>

                <!-- KPI 3: Published Live -->
                <button 
                    type="button" 
                    @click="currentTab = 'submissions'"
                    class="group text-left rounded-2xl border bg-white p-5 shadow-2xs transition hover:shadow-xs focus:outline-none cursor-pointer border-slate-200/90 hover:border-emerald-500"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">वेबसाइट पर प्रकाशित</span>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 group-hover:bg-emerald-600 group-hover:text-white transition">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-extrabold text-emerald-700 font-serif">{{ $stats['published_count'] }}</span>
                        <span class="text-xs text-slate-400">Live</span>
                    </div>
                    <div class="mt-1 text-[11px] text-emerald-600/80">
                        आपके नाम के साथ लाइव प्रदर्शित
                    </div>
                </button>

                <!-- KPI 4: Favorites Saved -->
                <button 
                    type="button" 
                    @click="currentTab = 'favorites'"
                    class="group text-left rounded-2xl border bg-white p-5 shadow-2xs transition hover:shadow-xs focus:outline-none cursor-pointer"
                    :class="currentTab === 'favorites' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-200/90 hover:border-rose-400'"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">पसंदीदा (Favorites)</span>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-600 border border-rose-200 group-hover:bg-rose-600 group-hover:text-white transition">
                            <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-serif">{{ $stats['favorites_count'] }}</span>
                        <span class="text-xs text-slate-400">Saved</span>
                    </div>
                    <div class="mt-1 text-[11px] text-slate-400">
                        सहेजी गई पठन पुस्तकें
                    </div>
                </button>
            </div>

            <!-- 2. Interactive Navigation Tabs -->
            <div class="border-b border-slate-200">
                <nav class="flex space-x-6 overflow-x-auto pb-px text-sm font-medium">
                    <button 
                        type="button" 
                        @click="currentTab = 'overview'" 
                        class="pb-3 border-b-2 transition whitespace-nowrap cursor-pointer"
                        :class="currentTab === 'overview' ? 'border-amber-600 text-amber-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-900'"
                    >
                        अवलोकन (Overview)
                    </button>
                    <button 
                        type="button" 
                        @click="currentTab = 'submissions'" 
                        class="pb-3 border-b-2 transition whitespace-nowrap cursor-pointer"
                        :class="currentTab === 'submissions' ? 'border-amber-600 text-amber-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-900'"
                    >
                        मेरी प्रस्तुतियां (My Submissions - {{ $stats['total_contributions'] }})
                    </button>
                    <button 
                        type="button" 
                        @click="currentTab = 'favorites'" 
                        class="pb-3 border-b-2 transition whitespace-nowrap cursor-pointer"
                        :class="currentTab === 'favorites' ? 'border-amber-600 text-amber-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-900'"
                    >
                        पसंदीदा साहित्य (Favorites - {{ $stats['favorites_count'] }})
                    </button>
                    <button 
                        type="button" 
                        @click="currentTab = 'reviews'" 
                        class="pb-3 border-b-2 transition whitespace-nowrap cursor-pointer"
                        :class="currentTab === 'reviews' ? 'border-amber-600 text-amber-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-900'"
                    >
                        समीक्षाएं (Reviews - {{ $stats['reviews_count'] }})
                    </button>
                    <button 
                        type="button" 
                        @click="currentTab = 'rewards'" 
                        class="pb-3 border-b-2 transition whitespace-nowrap cursor-pointer flex items-center gap-1.5"
                        :class="currentTab === 'rewards' ? 'border-amber-600 text-amber-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-900'"
                    >
                        <x-app-icon name="trophy" class="w-4 h-4 text-amber-600" />
                        <span>अंक एवं स्तर (Rewards & Level - {{ number_format($user->total_points) }} pts)</span>
                    </button>
                    <button 
                        type="button" 
                        @click="currentTab = 'attribution'" 
                        class="pb-3 border-b-2 transition whitespace-nowrap cursor-pointer"
                        :class="currentTab === 'attribution' ? 'border-amber-600 text-amber-700 font-bold' : 'border-transparent text-slate-500 hover:text-slate-900'"
                    >
                        योगदानकर्ता पहचान (Attribution)
                    </button>
                </nav>
            </div>

            <!-- TAB 1: OVERVIEW -->
            <div x-show="currentTab === 'overview'" class="space-y-8">
                <!-- Welcome & Contributor Editorial Notice -->
                <div class="rounded-2xl border border-amber-200/80 bg-gradient-to-br from-amber-50/70 via-white to-warm-50/50 p-6 sm:p-8 shadow-xs">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="max-w-3xl space-y-2">
                            <div class="inline-flex items-center gap-2 rounded-full bg-amber-100/80 px-3 py-1 text-xs font-semibold text-amber-900 border border-amber-200/70">
                                <span class="h-2 w-2 rounded-full bg-amber-600 animate-pulse"></span>
                                <span>डिजिटल लाइब्रेरी योगदान मंच • Editorial Review Workflow</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 font-serif">
                                अपनी संस्कृति, साहित्य और ज्ञान को डिजिटल रूप में सहेजें
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                आपके द्वारा सबमिट की गई पुस्तकें, ऑडियो एवं कथाएं हमारी संपादकीय टीम द्वारा जांची जाती हैं। स्वीकृति के पश्चात वे सार्वजनिक रूप से वेबसाइट पर आपके नाम 
                                (<span class="font-semibold text-amber-900">"योगदानकर्ता: {{ $user->public_display_name }}"</span>) 
                                के साथ प्रकाशित होती हैं।
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2.5 shrink-0">
                            <a 
                                href="{{ route('dashboard.books.create') }}" 
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white hover:bg-slate-800 shadow-xs transition"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                <span>नई पुस्तक जोड़ें</span>
                            </a>
                            <button 
                                type="button" 
                                @click="currentTab = 'submissions'" 
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                            >
                                प्रस्तुतियां देखें ({{ $stats['total_contributions'] }})
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contributor Recognition, Level Progress & Real Literary Reach Card -->
                <div class="rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-7 shadow-xs space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 text-white flex items-center justify-center shadow-sm shrink-0">
                                <x-app-icon :name="$user->level_config['icon'] ?? 'sparkles'" class="w-7 h-7" />
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700">वर्तमान स्तर (Current Level)</span>
                                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-extrabold text-amber-950 border border-amber-300">
                                        {{ $user->level_config['short_label'] }}
                                    </span>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-serif mt-0.5">
                                    {{ $user->level_config['label'] }}
                                </h3>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <button 
                                type="button" 
                                @click="openLedgerModal = true"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 px-3.5 py-2 text-xs font-semibold text-slate-700 transition cursor-pointer"
                            >
                                <x-app-icon name="file-text" class="w-4 h-4 text-slate-500" />
                                <span>अंक बहीखाता (Points Ledger)</span>
                            </button>
                            <button 
                                type="button" 
                                @click="currentTab = 'rewards'"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 px-4 py-2 text-xs font-bold text-white shadow-xs transition cursor-pointer"
                            >
                                <x-app-icon name="trophy" class="w-4 h-4" />
                                <span>सभी स्तर व रेफरल देखें</span>
                            </button>
                        </div>
                    </div>

                    <!-- 3 Metric Highlights -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Points -->
                        <div class="rounded-xl border border-slate-100 bg-amber-50/40 p-4">
                            <div class="flex items-center justify-between text-xs font-medium text-amber-900">
                                <span>संचित लाइब्रेरी अंक</span>
                                <x-app-icon name="trophy" class="w-4 h-4 text-amber-600" />
                            </div>
                            <div class="mt-2 flex items-baseline gap-1.5">
                                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-serif">{{ number_format($user->total_points) }}</span>
                                <span class="text-xs font-semibold text-amber-700">अंक</span>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-500">
                                स्वीकृत साहित्य प्रकाशन के आधार पर
                            </p>
                        </div>

                        <!-- Approved Contributions -->
                        <div class="rounded-xl border border-slate-100 bg-emerald-50/40 p-4">
                            <div class="flex items-center justify-between text-xs font-medium text-emerald-900">
                                <span>स्वीकृत साहित्य कृतियां</span>
                                <x-app-icon name="check-circle" class="w-4 h-4 text-emerald-600" />
                            </div>
                            <div class="mt-2 flex items-baseline gap-1.5">
                                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-serif">{{ $contributorStats['approved_count'] }}</span>
                                <span class="text-xs font-semibold text-emerald-700">प्रकाशित</span>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-500">
                                सार्वजनिक डिजिटल पुस्तकालय में सक्रिय
                            </p>
                        </div>

                        <!-- Genuine Literary Impact / Reader Reach -->
                        <div class="rounded-xl border border-slate-100 bg-cyan-50/40 p-4">
                            <div class="flex items-center justify-between text-xs font-medium text-cyan-900">
                                <span>पाठक पहुंच (Literary Reach)</span>
                                <x-app-icon name="book-open" class="w-4 h-4 text-cyan-700" />
                            </div>
                            <div class="mt-2 flex items-baseline gap-1.5">
                                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-serif">{{ number_format($contributorStats['reader_reach']) }}</span>
                                <span class="text-xs font-semibold text-cyan-700">पठन / श्रवण</span>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-500">
                                आपके साहित्य का वास्तविक पाठक जुड़ाव
                            </p>
                        </div>
                    </div>

                    <!-- Level Progress Bar -->
                    @if ($user->next_level_config)
                        <div class="rounded-xl border border-slate-200/70 bg-slate-50/60 p-4 space-y-2.5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-xs">
                                <div>
                                    <span class="font-bold text-slate-800">अगला स्तर लक्ष्य:</span>
                                    <span class="font-semibold text-amber-700">{{ $user->next_level_config['label'] }}</span>
                                    <span class="text-slate-400">({{ $user->next_level_config['min_approved'] }} स्वीकृत रचनाएं)</span>
                                </div>
                                <div class="text-slate-600 font-medium">
                                    अगले स्तर के लिए <span class="font-bold text-amber-700">{{ $user->progress_to_next_level['remaining'] }}</span> और रचनाएं आवश्यक
                                </div>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                <div 
                                    class="bg-gradient-to-r from-amber-500 to-amber-600 h-2.5 rounded-full transition-all duration-500" 
                                    style="width: {{ $user->progress_to_next_level['percentage'] }}%"
                                ></div>
                            </div>
                            <div class="flex justify-between text-[11px] text-slate-400 font-medium">
                                <span>वर्तमान: {{ $user->progress_to_next_level['current'] }} स्वीकृत रचनाएं</span>
                                <span>प्रगति: {{ $user->progress_to_next_level['percentage'] }}% पूर्ण</span>
                            </div>
                        </div>
                    @else
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-3.5 flex items-center gap-3 text-xs text-emerald-900 font-medium">
                            <x-app-icon name="sparkles" class="w-5 h-5 text-emerald-600 shrink-0" />
                            <span>बधाई! आप डिजिटल हिन्दी पुस्तकालय के सर्वोच्च योगदानकर्ता स्तर <strong>({{ $user->level_config['label'] }})</strong> पर पहुंच चुके हैं। हिन्दी साहित्य संवर्धन में आपका अमूल्य योगदान वंदनीय है!</span>
                        </div>
                    @endif
                </div>

                <!-- 6 Quick Contribution Cards Grid -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-serif">
                                नया साहित्य जोड़ें (Contribute Content)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                छह विशिष्ट श्रेणियों में अपना योगदान दें — सभी प्रस्तुतियां गुणवत्ता समीक्षा के बाद लाइव होंगी।
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- 1. Book PDF -->
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 hover:border-amber-300 hover:shadow-md transition group flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/70 flex items-center justify-center group-hover:scale-105 transition">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">PDF / ग्रंथ</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-amber-700 transition font-serif">
                                    पुस्तक जोड़ें (Add Book)
                                </h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    सार्वजनिक डोमेन या अप्रतिबंधित पीडीएफ पुस्तकें, शोध पत्र व दुर्लभ पांडुलिपियां जोड़ें।
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400">{{ $stats['books_count'] }} जोड़ी गईं</span>
                                <a 
                                    href="{{ route('dashboard.books.create') }}" 
                                    class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 hover:text-amber-900"
                                >
                                    <span>+ जोड़ें</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>

                        <!-- 2. Audiobook -->
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 hover:border-violet-300 hover:shadow-md transition group flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="h-10 w-10 rounded-xl bg-violet-50 text-violet-700 border border-violet-200/70 flex items-center justify-center group-hover:scale-105 transition">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.757 3.63 8.25 4.51 8.25H6.75z" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Audio / वाचन</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-violet-700 transition font-serif">
                                    ऑडियोबुक जोड़ें (Add Audiobook)
                                </h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    ध्वनि पुस्तक, वाचन, अध्यायवार ऑडियो फाइलें एवं पॉडकास्ट सामग्री सबमिट करें।
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400">{{ $stats['audiobooks_count'] }} जोड़ी गईं</span>
                                <a 
                                    href="{{ route('dashboard.submissions.audiobook.create') }}" 
                                    class="inline-flex items-center gap-1 text-xs font-bold text-violet-700 hover:text-violet-900"
                                >
                                    <span>+ जोड़ें</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>

                        <!-- 3. Chalisa -->
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 hover:border-rose-300 hover:shadow-md transition group flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="h-10 w-10 rounded-xl bg-rose-50 text-rose-700 border border-rose-200/70 flex items-center justify-center group-hover:scale-105 transition">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">भक्ति स्तुति</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-rose-700 transition font-serif">
                                    चालीसा जोड़ें (Add Chalisa)
                                </h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    देवी-देवताओं की पावन चालीसा, दोहे, चौपाइयां एवं भावार्थ संग्रह में प्रस्तुत करें।
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400">भक्ति अनुभाग</span>
                                <a 
                                    href="{{ route('dashboard.submissions.chalisa.create') }}" 
                                    class="inline-flex items-center gap-1 text-xs font-bold text-rose-700 hover:text-rose-900"
                                >
                                    <span>+ जोड़ें</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>

                        <!-- 4. Aarti -->
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 hover:border-amber-300 hover:shadow-md transition group flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/70 flex items-center justify-center group-hover:scale-105 transition">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">देव वंदना</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-amber-700 transition font-serif">
                                    आरती जोड़ें (Add Aarti)
                                </h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    प्रामाणिक आरती पाठ, स्तुति मंत्र व भावपूर्ण देव वंदना साहित्य प्रस्तुत करें।
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400">आरती संग्रह</span>
                                <a 
                                    href="{{ route('dashboard.submissions.aarti.create') }}" 
                                    class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 hover:text-amber-900"
                                >
                                    <span>+ जोड़ें</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>

                        <!-- 5. Children's Story -->
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 hover:border-emerald-300 hover:shadow-md transition group flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/70 flex items-center justify-center group-hover:scale-105 transition">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">बाल साहित्य</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition font-serif">
                                    बाल कहानी जोड़ें (Kids Story)
                                </h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    बच्चों के लिए रोचक, शिक्षाप्रद, नैतिक व ज्ञानवर्धक बाल कहानियां एवं लघुकथाएं।
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400">शिशु व बाल जगत</span>
                                <a 
                                    href="{{ route('dashboard.submissions.story.create', ['type' => 'children']) }}" 
                                    class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-900"
                                >
                                    <span>+ जोड़ें</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>

                        <!-- 6. General / Folk / Mythological Story -->
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 hover:border-cyan-300 hover:shadow-md transition group flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="h-10 w-10 rounded-xl bg-cyan-50 text-cyan-700 border border-cyan-200/70 flex items-center justify-center group-hover:scale-105 transition">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">कथा एवं आख्यान</span>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-cyan-700 transition font-serif">
                                    कथा व लोककथा (Stories)
                                </h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    ऐतिहासिक, पौराणिक, प्रेरक, आंचलिक एवं धारावाहिक कहानियां प्रस्तुत करें।
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] text-slate-400">कथा श्रृंखला</span>
                                <a 
                                    href="{{ route('dashboard.submissions.story.create', ['type' => 'general']) }}" 
                                    class="inline-flex items-center gap-1 text-xs font-bold text-cyan-700 hover:text-cyan-900"
                                >
                                    <span>+ जोड़ें</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Submissions Across Formats -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">
                                हाल ही में जोड़ी गई सामग्री (Recent Submissions Feed)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">आपके सभी प्रारूपों में हालिया प्रस्तुतियां एवं उनकी वर्तमान समीक्षा स्थिति</p>
                        </div>
                        <button type="button" @click="currentTab = 'submissions'" class="text-xs font-bold text-amber-700 hover:text-amber-900">
                            सभी देखें ({{ $stats['total_contributions'] }}) &rarr;
                        </button>
                    </div>

                    @php
                        $recentList = collect();
                        foreach ($submittedBooks->take(3) as $item) {
                            $recentList->push(['type' => 'पुस्तक (Book)', 'model' => $item, 'badge_color' => 'amber', 'url' => $item->isPublished() ? route('books.show', $item->slug) : null]);
                        }
                        foreach ($submittedAudiobooks->take(3) as $item) {
                            $recentList->push(['type' => 'ऑडियोबुक', 'model' => $item, 'badge_color' => 'violet', 'url' => $item->isPublished() ? url('/audiobooks/' . $item->slug) : null]);
                        }
                        foreach ($submittedDevotionals->take(3) as $item) {
                            $recentList->push(['type' => $item->type === 'chalisa' ? 'चालीसा' : 'आरती', 'model' => $item, 'badge_color' => 'rose', 'url' => $item->isPublished() ? url('/' . $item->type . '/' . $item->slug) : null]);
                        }
                        foreach ($submittedStories->take(3) as $item) {
                            $recentList->push(['type' => $item->story_type === 'children' ? 'बाल कहानी' : 'कथा श्रृंखला', 'model' => $item, 'badge_color' => 'cyan', 'url' => $item->isPublished() ? url('/stories/' . $item->slug) : null]);
                        }
                        $recentList = $recentList->sortByDesc(fn($x) => $x['model']->id)->take(5);
                    @endphp

                    @if ($recentList->isNotEmpty())
                        <div class="divide-y divide-slate-100">
                            @foreach ($recentList as $entry)
                                @php $item = $entry['model']; @endphp
                                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 shrink-0 mt-0.5">
                                            {{ $entry['type'] }}
                                        </span>
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-900">
                                                @if ($entry['url'])
                                                    <a href="{{ $entry['url'] }}" class="hover:text-amber-700 transition">
                                                        {{ $item->title }}
                                                    </a>
                                                @else
                                                    <span>{{ $item->title }}</span>
                                                @endif
                                            </h4>
                                            <p class="text-[11px] text-slate-400 mt-0.5">
                                                सबमिट दिनांक: {{ $item->created_at->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 shrink-0 self-end sm:self-auto">
                                        @if ($item->status === 'published' || (method_exists($item, 'isPublished') && $item->isPublished()))
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                                ✓ Live / स्वीकृत
                                            </span>
                                        @elseif ($item->status === 'revision_required')
                                            <span class="inline-flex items-center rounded-full bg-orange-50 px-2.5 py-0.5 text-xs font-semibold text-orange-700 border border-orange-200">
                                                ⚠ संशोधन अपेक्षित
                                            </span>
                                        @elseif ($item->status === 'rejected')
                                            <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-700 border border-rose-200">
                                                ✕ अस्वीकृत
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 border border-amber-200">
                                                ⏳ समीक्षाधीन (Under Review)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-10 text-center text-xs text-slate-500">
                            <p>आपने अभी तक कोई सामग्री सबमिट नहीं की है।</p>
                            <p class="mt-1 text-slate-400">ऊपर दिए गए कार्ड्स में से किसी भी प्रारूप का चयन कर अपना पहला योगदान दें।</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 2: UNIFIED MULTI-FORMAT SUBMISSIONS -->
            <div x-show="currentTab === 'submissions'" style="display: none;" class="space-y-6" x-data="{ subFilter: 'all' }">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 font-serif">
                            मेरी प्रस्तुतियां (My Submissions Repository)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            आपके द्वारा सबमिट की गई पुस्तकें, ऑडियो, चालीसा, आरती व कहानियों की समीक्षा स्थिति एवं संपादन।
                        </p>
                    </div>

                    <!-- Universal Contribute Dropdown Trigger -->
                    <div class="relative" x-data="{ addMenuOpen: false }">
                        <button 
                            type="button" 
                            @click="addMenuOpen = !addMenuOpen" 
                            @click.outside="addMenuOpen = false"
                            class="inline-flex items-center gap-2 rounded-xl bg-amber-700 px-4 py-2 text-xs font-bold text-white hover:bg-amber-800 shadow-xs transition cursor-pointer"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>नया साहित्य जोड़ें</span>
                            <svg class="h-3.5 w-3.5 text-amber-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <div 
                            x-show="addMenuOpen" 
                            x-cloak 
                            x-transition
                            class="absolute right-0 mt-2 w-64 rounded-xl bg-white p-2 shadow-xl border border-slate-200 z-50 text-xs"
                        >
                            <a href="{{ route('dashboard.books.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="book-open" class="h-4 w-4 text-amber-700 shrink-0" />
                                <div>
                                    <div class="font-bold">पुस्तक (Book PDF)</div>
                                    <div class="text-[10px] text-slate-400">दुर्लभ ग्रंथ व ई-पुस्तक</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.submissions.audiobook.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-violet-50 text-slate-700 hover:text-violet-900 transition">
                                <x-app-icon name="headphones" class="h-4 w-4 text-violet-700 shrink-0" />
                                <div>
                                    <div class="font-bold">ऑडियोबुक (Audiobook)</div>
                                    <div class="text-[10px] text-slate-400">वाचन, ध्वनि कथा व अध्याय</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.submissions.chalisa.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-rose-50 text-slate-700 hover:text-rose-900 transition">
                                <x-app-icon name="flame" class="h-4 w-4 text-rose-700 shrink-0" />
                                <div>
                                    <div class="font-bold">श्री चालीसा (Chalisa)</div>
                                    <div class="text-[10px] text-slate-400">दोहे, चौपाई व भक्ति स्तुति</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.submissions.aarti.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-amber-50 text-slate-700 hover:text-amber-900 transition">
                                <x-app-icon name="sparkles" class="h-4 w-4 text-amber-700 shrink-0" />
                                <div>
                                    <div class="font-bold">आरती संग्रह (Aarti)</div>
                                    <div class="text-[10px] text-slate-400">पावन आरती व देव वंदना</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.submissions.story.create', ['type' => 'children']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-emerald-50 text-slate-700 hover:text-emerald-900 transition">
                                <x-app-icon name="sparkles" class="h-4 w-4 text-emerald-600 shrink-0" />
                                <div>
                                    <div class="font-bold">बाल कहानी (Kids Story)</div>
                                    <div class="text-[10px] text-slate-400">रोचक व शिक्षाप्रद बाल कथाएं</div>
                                </div>
                            </a>
                            <a href="{{ route('dashboard.submissions.story.create', ['type' => 'general']) }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-cyan-50 text-slate-700 hover:text-cyan-900 transition">
                                <x-app-icon name="scroll-text" class="h-4 w-4 text-cyan-700 shrink-0" />
                                <div>
                                    <div class="font-bold">कथा व लोककथा (Stories)</div>
                                    <div class="text-[10px] text-slate-400">पौराणिक व सामाजिक कहानियां</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Sub-filters Pill Navigation -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                    <button 
                        type="button" 
                        @click="subFilter = 'all'" 
                        class="px-3.5 py-1.5 rounded-lg font-medium transition whitespace-nowrap cursor-pointer"
                        :class="subFilter === 'all' ? 'bg-slate-900 text-white shadow-xs font-bold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'"
                    >
                        सभी (All - {{ $stats['total_contributions'] }})
                    </button>
                    <button 
                        type="button" 
                        @click="subFilter = 'books'" 
                        class="px-3.5 py-1.5 rounded-lg font-medium transition whitespace-nowrap cursor-pointer"
                        :class="subFilter === 'books' ? 'bg-slate-900 text-white shadow-xs font-bold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'"
                    >
                        पुस्तकें (Books - {{ $stats['books_count'] }})
                    </button>
                    <button 
                        type="button" 
                        @click="subFilter = 'audiobooks'" 
                        class="px-3.5 py-1.5 rounded-lg font-medium transition whitespace-nowrap cursor-pointer"
                        :class="subFilter === 'audiobooks' ? 'bg-slate-900 text-white shadow-xs font-bold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'"
                    >
                        ऑडियोबुक्स (Audiobooks - {{ $stats['audiobooks_count'] }})
                    </button>
                    <button 
                        type="button" 
                        @click="subFilter = 'devotionals'" 
                        class="px-3.5 py-1.5 rounded-lg font-medium transition whitespace-nowrap cursor-pointer"
                        :class="subFilter === 'devotionals' ? 'bg-slate-900 text-white shadow-xs font-bold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'"
                    >
                        चालीसा व आरती ({{ $stats['devotionals_count'] }})
                    </button>
                    <button 
                        type="button" 
                        @click="subFilter = 'stories'" 
                        class="px-3.5 py-1.5 rounded-lg font-medium transition whitespace-nowrap cursor-pointer"
                        :class="subFilter === 'stories' ? 'bg-slate-900 text-white shadow-xs font-bold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'"
                    >
                        कहानियां (Stories - {{ $stats['stories_count'] }})
                    </button>
                </div>

                @if ($stats['total_contributions'] === 0)
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 mb-3">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">कोई प्रस्तुति उपलब्ध नहीं है</h3>
                        <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                            आप अपनी पुस्तकें, ऑडियो, चालीसा, आरती व कहानियां सबमिट कर डिजिटल लाइब्रेरी के निर्माण में योगदान दे सकते हैं।
                        </p>
                        <div class="mt-4">
                            <a 
                                href="{{ route('dashboard.books.create') }}" 
                                class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 shadow-xs transition"
                            >
                                + अपनी पहली पुस्तक जोड़ें
                            </a>
                        </div>
                    </div>
                @endif

                <!-- SECTION 1: BOOKS TABLE -->
                <div x-show="subFilter === 'all' || subFilter === 'books'" class="space-y-3">
                    @if ($submittedBooks->isNotEmpty())
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 font-serif flex items-center gap-2">
                                <x-app-icon name="book-open" class="h-4 w-4 text-amber-700" />
                                <span>पुस्तकें (Books)</span>
                                <span class="text-xs font-normal text-slate-500">({{ $submittedBooks->count() }})</span>
                            </h3>
                        </div>
                        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs">
                            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                                <thead class="bg-slate-50 font-bold uppercase tracking-wider text-slate-600">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 sm:pl-6">शीर्षक (Title)</th>
                                        <th scope="col" class="px-3 py-3.5">लेखक / श्रेणी</th>
                                        <th scope="col" class="px-3 py-3.5">समीक्षा स्थिति</th>
                                        <th scope="col" class="px-3 py-3.5">तारीख</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right">कार्य</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                                    @foreach ($submittedBooks as $book)
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="py-3.5 pl-4 pr-3 sm:pl-6">
                                                <div class="font-bold text-slate-900">
                                                    <a href="{{ route('dashboard.submissions.show', ['book', $book->id]) }}" class="hover:text-amber-700 transition">
                                                        {{ $book->title }}
                                                    </a>
                                                </div>
                                                @if ($book->user_facing_feedback)
                                                    <div class="mt-1.5 rounded-lg bg-orange-50 p-2 text-[11px] text-orange-900 border border-orange-200 leading-normal">
                                                        <span class="font-bold">संपादकीय टिप्पणी:</span> {{ $book->user_facing_feedback }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3.5 text-slate-600">
                                                <div>{{ $book->authors->pluck('name')->join(', ') ?: '—' }}</div>
                                                <div class="text-[11px] text-slate-400">{{ $book->categories->pluck('name')->join(', ') ?: '—' }}</div>
                                            </td>
                                            <td class="px-3 py-3.5">
                                                @if ($book->isPublished())
                                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 border border-emerald-200">
                                                        ✓ Published
                                                    </span>
                                                @elseif ($book->status === 'revision_required')
                                                    <span class="inline-flex items-center rounded-full bg-orange-50 px-2 py-0.5 text-[11px] font-semibold text-orange-700 border border-orange-200">
                                                        ⚠ Revision
                                                    </span>
                                                @elseif ($book->status === 'rejected')
                                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700 border border-rose-200">
                                                        ✕ Rejected
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 border border-amber-200">
                                                        ⏳ Under Review
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3.5 text-slate-400">
                                                {{ $book->created_at->format('d M Y') }}
                                            </td>
                                            <td class="py-3.5 pl-3 pr-4 sm:pr-6 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('dashboard.submissions.show', ['book', $book->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition">
                                                        विवरण &rarr;
                                                    </a>
                                                    @if (! $book->isPublished())
                                                        <form action="{{ route('dashboard.books.destroy', $book) }}" method="POST" onsubmit="return confirm('क्या आप इस पुस्तक सबमिशन को हटाना चाहते हैं?');" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-rose-600 hover:text-rose-900 font-semibold transition cursor-pointer p-1">
                                                                हटाएं
                                                            </button>
                                                        </form>
                                                    @else
                                                        <a href="{{ route('books.show', $book->slug) }}" class="text-amber-700 hover:text-amber-900 font-semibold transition px-1">
                                                            लाइव &rarr;
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- SECTION 2: AUDIOBOOKS TABLE -->
                <div x-show="subFilter === 'all' || subFilter === 'audiobooks'" class="space-y-3">
                    @if ($submittedAudiobooks->isNotEmpty())
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 font-serif flex items-center gap-2">
                                <x-app-icon name="headphones" class="h-4 w-4 text-violet-700" />
                                <span>ऑडियोबुक्स (Audiobooks)</span>
                                <span class="text-xs font-normal text-slate-500">({{ $submittedAudiobooks->count() }})</span>
                            </h3>
                        </div>
                        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs">
                            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                                <thead class="bg-slate-50 font-bold uppercase tracking-wider text-slate-600">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 sm:pl-6">शीर्षक (Title)</th>
                                        <th scope="col" class="px-3 py-3.5">वाचक / लेखक</th>
                                        <th scope="col" class="px-3 py-3.5">समीक्षा स्थिति</th>
                                        <th scope="col" class="px-3 py-3.5">तारीख</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right">कार्य</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                                    @foreach ($submittedAudiobooks as $audiobook)
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="py-3.5 pl-4 pr-3 sm:pl-6">
                                                <div class="font-bold text-slate-900">
                                                    <a href="{{ route('dashboard.submissions.show', ['audiobook', $audiobook->id]) }}" class="hover:text-violet-700 transition">
                                                        {{ $audiobook->title }}
                                                    </a>
                                                </div>
                                                @if ($audiobook->user_facing_feedback)
                                                    <div class="mt-1.5 rounded-lg bg-orange-50 p-2 text-[11px] text-orange-900 border border-orange-200 leading-normal">
                                                        <span class="font-bold">संपादकीय टिप्पणी:</span> {{ $audiobook->user_facing_feedback }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3.5 text-slate-600">
                                                <div>{{ $audiobook->narrator_name ?: '—' }}</div>
                                                <div class="text-[11px] text-slate-400">{{ $audiobook->author_name ?: '—' }}</div>
                                            </td>
                                            <td class="px-3 py-3.5">
                                                @if ($audiobook->status === 'published')
                                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 border border-emerald-200">
                                                        ✓ Published
                                                    </span>
                                                @elseif ($audiobook->status === 'revision_required')
                                                    <span class="inline-flex items-center rounded-full bg-orange-50 px-2 py-0.5 text-[11px] font-semibold text-orange-700 border border-orange-200">
                                                        ⚠ Revision
                                                    </span>
                                                @elseif ($audiobook->status === 'rejected')
                                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700 border border-rose-200">
                                                        ✕ Rejected
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 border border-amber-200">
                                                        ⏳ Under Review
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3.5 text-slate-400">
                                                {{ $audiobook->created_at->format('d M Y') }}
                                            </td>
                                            <td class="py-3.5 pl-3 pr-4 sm:pr-6 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('dashboard.submissions.show', ['audiobook', $audiobook->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition">
                                                        विवरण &rarr;
                                                    </a>
                                                    <a href="{{ route('dashboard.submissions.audiobook.chapters', $audiobook) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg border border-violet-200 text-violet-700 hover:bg-violet-50 font-semibold text-xs transition">
                                                        अध्याय ({{ $audiobook->chapters->count() }})
                                                    </a>
                                                    @if ($audiobook->status !== 'published')
                                                        <form action="{{ route('dashboard.submissions.audiobook.destroy', $audiobook) }}" method="POST" onsubmit="return confirm('क्या आप इस ऑडियोबुक सबमिशन को हटाना चाहते हैं?');" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-rose-600 hover:text-rose-900 font-semibold transition cursor-pointer p-1">
                                                                हटाएं
                                                            </button>
                                                        </form>
                                                    @else
                                                        <a href="{{ url('/audiobooks/' . $audiobook->slug) }}" class="text-violet-700 hover:text-violet-900 font-semibold transition px-1">
                                                            सुनें &rarr;
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- SECTION 3: DEVOTIONALS (CHALISA & AARTI) TABLE -->
                <div x-show="subFilter === 'all' || subFilter === 'devotionals'" class="space-y-3">
                    @if ($submittedDevotionals->isNotEmpty())
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 font-serif flex items-center gap-2">
                                <x-app-icon name="flame" class="h-4 w-4 text-rose-700" />
                                <span>चालीसा व आरती (Devotionals)</span>
                                <span class="text-xs font-normal text-slate-500">({{ $submittedDevotionals->count() }})</span>
                            </h3>
                        </div>
                        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs">
                            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                                <thead class="bg-slate-50 font-bold uppercase tracking-wider text-slate-600">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 sm:pl-6">शीर्षक (Title)</th>
                                        <th scope="col" class="px-3 py-3.5">प्रकार / देव</th>
                                        <th scope="col" class="px-3 py-3.5">समीक्षा स्थिति</th>
                                        <th scope="col" class="px-3 py-3.5">तारीख</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right">कार्य</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                                    @foreach ($submittedDevotionals as $devotional)
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="py-3.5 pl-4 pr-3 sm:pl-6">
                                                <div class="font-bold text-slate-900">
                                                    <a href="{{ route('dashboard.submissions.show', ['devotional', $devotional->id]) }}" class="hover:text-rose-700 transition">
                                                        {{ $devotional->title }}
                                                    </a>
                                                </div>
                                                @if ($devotional->user_facing_feedback)
                                                    <div class="mt-1.5 rounded-lg bg-orange-50 p-2 text-[11px] text-orange-900 border border-orange-200 leading-normal">
                                                        <span class="font-bold">संपादकीय टिप्पणी:</span> {{ $devotional->user_facing_feedback }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3.5 text-slate-600">
                                                <span class="capitalize font-semibold">{{ $devotional->type === 'chalisa' ? 'चालीसा' : 'आरती' }}</span>
                                                <div class="text-[11px] text-slate-400">{{ $devotional->deity ?: '—' }}</div>
                                            </td>
                                            <td class="px-3 py-3.5">
                                                @if ($devotional->status === 'published')
                                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 border border-emerald-200">
                                                        ✓ Published
                                                    </span>
                                                @elseif ($devotional->status === 'revision_required')
                                                    <span class="inline-flex items-center rounded-full bg-orange-50 px-2 py-0.5 text-[11px] font-semibold text-orange-700 border border-orange-200">
                                                        ⚠ Revision
                                                    </span>
                                                @elseif ($devotional->status === 'rejected')
                                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700 border border-rose-200">
                                                        ✕ Rejected
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 border border-amber-200">
                                                        ⏳ Under Review
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3.5 text-slate-400">
                                                {{ $devotional->created_at->format('d M Y') }}
                                            </td>
                                            <td class="py-3.5 pl-3 pr-4 sm:pr-6 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('dashboard.submissions.show', ['devotional', $devotional->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition">
                                                        विवरण &rarr;
                                                    </a>
                                                    @if ($devotional->status !== 'published')
                                                        <form action="{{ route('dashboard.submissions.devotional.destroy', $devotional) }}" method="POST" onsubmit="return confirm('क्या आप इस रचना को हटाना चाहते हैं?');" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-rose-600 hover:text-rose-900 font-semibold transition cursor-pointer p-1">
                                                                हटाएं
                                                            </button>
                                                        </form>
                                                    @else
                                                        <a href="{{ url('/' . $devotional->type . '/' . $devotional->slug) }}" class="text-rose-700 hover:text-rose-900 font-semibold transition px-1">
                                                            पढ़ें &rarr;
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- SECTION 4: STORIES TABLE -->
                <div x-show="subFilter === 'all' || subFilter === 'stories'" class="space-y-3">
                    @if ($submittedStories->isNotEmpty())
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 font-serif flex items-center gap-2">
                                <x-app-icon name="scroll-text" class="h-4 w-4 text-cyan-700" />
                                <span>कहानियां व बाल साहित्य (Stories)</span>
                                <span class="text-xs font-normal text-slate-500">({{ $submittedStories->count() }})</span>
                            </h3>
                        </div>
                        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-2xs">
                            <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                                <thead class="bg-slate-50 font-bold uppercase tracking-wider text-slate-600">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 sm:pl-6">शीर्षक (Title)</th>
                                        <th scope="col" class="px-3 py-3.5">प्रकार / एपिसोड्स</th>
                                        <th scope="col" class="px-3 py-3.5">समीक्षा स्थिति</th>
                                        <th scope="col" class="px-3 py-3.5">तारीख</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right">कार्य</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                                    @foreach ($submittedStories as $story)
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="py-3.5 pl-4 pr-3 sm:pl-6">
                                                <div class="font-bold text-slate-900">
                                                    <a href="{{ route('dashboard.submissions.show', ['story', $story->id]) }}" class="hover:text-cyan-700 transition">
                                                        {{ $story->title }}
                                                    </a>
                                                </div>
                                                @if ($story->user_facing_feedback)
                                                    <div class="mt-1.5 rounded-lg bg-orange-50 p-2 text-[11px] text-orange-900 border border-orange-200 leading-normal">
                                                        <span class="font-bold">संपादकीय टिप्पणी:</span> {{ $story->user_facing_feedback }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3.5 text-slate-600">
                                                <span class="font-semibold">{{ $story->story_type === 'children' ? 'बाल साहित्य' : 'सामान्य कथा' }}</span>
                                                <div class="text-[11px] text-slate-400">{{ $story->episodes_count ?? $story->episodes->count() }} भाग / एपिसोड</div>
                                            </td>
                                            <td class="px-3 py-3.5">
                                                @if ($story->status === 'published')
                                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 border border-emerald-200">
                                                        ✓ Published
                                                    </span>
                                                @elseif ($story->status === 'revision_required')
                                                    <span class="inline-flex items-center rounded-full bg-orange-50 px-2 py-0.5 text-[11px] font-semibold text-orange-700 border border-orange-200">
                                                        ⚠ Revision
                                                    </span>
                                                @elseif ($story->status === 'rejected')
                                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-semibold text-rose-700 border border-rose-200">
                                                        ✕ Rejected
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 border border-amber-200">
                                                        ⏳ Under Review
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3.5 text-slate-400">
                                                {{ $story->created_at->format('d M Y') }}
                                            </td>
                                            <td class="py-3.5 pl-3 pr-4 sm:pr-6 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('dashboard.submissions.show', ['story', $story->id]) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition">
                                                        विवरण &rarr;
                                                    </a>
                                                    @if ($story->status !== 'published')
                                                        <form action="{{ route('dashboard.submissions.story.destroy', $story) }}" method="POST" onsubmit="return confirm('क्या आप इस कहानी को हटाना चाहते हैं?');" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-rose-600 hover:text-rose-900 font-semibold transition cursor-pointer p-1">
                                                                हटाएं
                                                            </button>
                                                        </form>
                                                    @else
                                                        <a href="{{ url('/stories/' . $story->slug) }}" class="text-cyan-700 hover:text-cyan-900 font-semibold transition px-1">
                                                            पढ़ें &rarr;
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 3: MY FAVORITES -->
            <div x-show="currentTab === 'favorites'" style="display: none;" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 font-serif">
                        मेरी पसंदीदा पुस्तकें (My Saved Books)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        यहाँ आपकी सहेजी गई पुस्तकें हैं जिन्हें आप कभी भी पढ़ या डाउनलोड कर सकते हैं।
                    </p>
                </div>

                @if ($favoriteBooks->isNotEmpty())
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                        @foreach ($favoriteBooks as $book)
                            <div class="relative group">
                                <x-book-card :book="$book" />
                                <form action="{{ route('dashboard.favorites.destroy', $book) }}" method="POST" class="mt-2 text-center">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] text-rose-500 hover:text-rose-700 font-medium transition cursor-pointer">
                                        पसंदीदा से हटाएं
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 mb-3">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">कोई पसंदीदा पुस्तक नहीं मिली</h3>
                        <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto">
                            पुस्तकालय में पुस्तकें खोजें और अपनी पसंदीदा रचनाओं को यहाँ सहेजें।
                        </p>
                        <div class="mt-4">
                            <a 
                                href="{{ route('books.index') }}" 
                                class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 shadow-xs transition"
                            >
                                पुस्तकें खोजें (Browse Books)
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- TAB 4: MY REVIEWS -->
            <div x-show="currentTab === 'reviews'" style="display: none;" class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 font-serif">
                        मेरी समीक्षाएं एवं रेटिंग्स (My Reviews)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        आपके द्वारा विभिन्न पुस्तकों पर लिखी गई पाठकीय समीक्षाएं।
                    </p>
                </div>

                @if ($reviews->isNotEmpty())
                    <div class="space-y-4">
                        @foreach ($reviews as $review)
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs space-y-2">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">
                                            @if ($review->book)
                                                <a href="{{ route('books.show', $review->book->slug) }}" class="hover:text-amber-700">
                                                    {{ $review->book->title }}
                                                </a>
                                            @else
                                                <span>Book</span>
                                            @endif
                                        </h4>
                                        <div class="flex items-center gap-1 mt-1 text-amber-500">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="h-4 w-4 {{ $i <= $review->rating ? 'fill-current' : 'text-slate-300' }}" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                            <span class="text-xs text-slate-400 ml-1">({{ $review->rating }}/5)</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if (isset($review->status->value) ? $review->status->value === 'approved' : $review->status === 'approved')
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 border border-emerald-200">
                                                Approved
                                            </span>
                                        @elseif (isset($review->status->value) ? $review->status->value === 'rejected' : $review->status === 'rejected')
                                            <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-medium text-rose-700 border border-rose-200">
                                                Rejected
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 border border-amber-200">
                                                Pending Moderation
                                            </span>
                                        @endif
                                        <span class="text-xs text-slate-400">
                                            {{ $review->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                                @if ($review->comment)
                                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pt-1">
                                        "{{ $review->comment }}"
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-xs text-slate-500">
                        <p>आपने अभी तक किसी पुस्तक की समीक्षा नहीं लिखी है।</p>
                        <p class="mt-1 text-slate-400">पुस्तकालय में पुस्तकें पढ़ें और साथी पाठकों के मार्गदर्शन हेतु अपने विचार साझा करें।</p>
                    </div>
                @endif
            </div>

            <!-- TAB 5: CONTRIBUTOR ATTRIBUTION & PROFILE PREVIEW -->
            <div x-show="currentTab === 'attribution'" style="display: none;" class="space-y-6">
                <div class="rounded-2xl border border-amber-200/80 bg-white p-6 sm:p-8 shadow-xs space-y-6">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900 border border-amber-200 mb-2">
                            <span>योगदानकर्ता पहचान एवं श्रेय (Public Attribution)</span>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 font-serif">
                            वेबसाइट पर आपका नाम कैसे प्रदर्शित होगा?
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed max-w-2xl">
                            जब आपकी सबमिट की गई पुस्तक, ऑडियोबुक, चालीसा या कहानी एडमिन टीम द्वारा स्वीकृत एवं प्रकाशित की जाती है, तो सार्वजनिक पेज पर आपका सार्वजनिक नाम श्रेय के रूप में प्रदर्शित किया जाता है।
                        </p>
                    </div>

                    <!-- Live Attribution Badge Preview -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-5 space-y-3">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-500">
                            लाइव प्रीव्यू (Public Badge Preview on Live Pages):
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <x-contributor-badge :user="$user" />
                            <span class="text-[11px] text-slate-400">
                                पाठक इस नाम से आपका योगदान पहचान सकेंगे
                            </span>
                        </div>
                    </div>

                    <!-- Profile Name Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="text-xs font-medium text-slate-400">वर्तमान सार्वजनिक नाम (Public Display Name)</div>
                            <div class="text-base font-bold text-slate-900 mt-1 font-serif">{{ $user->public_display_name }}</div>
                            <p class="text-[11px] text-slate-500 mt-1">
                                यह नाम आपकी सभी प्रकाशित पुस्तकों एवं रचनाओं पर योगदानकर्ता के रूप में दिखेगा।
                            </p>
                        </div>
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="text-xs font-medium text-slate-400">प्रोफाइल संपादन (Edit Name)</div>
                            <div class="mt-2">
                                <a 
                                    href="{{ route('profile') }}" 
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800 transition"
                                >
                                    <span>प्रोफाइल में नाम बदलें</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-2">
                                अपना सार्वजनिक नाम कभी भी अपनी प्रोफाइल सेटिंग्स में जाकर बदल सकते हैं।
                            </p>
                        </div>
                    </div>

                    <!-- Quality & Copyright Guidelines -->
                    <div class="border-t border-slate-100 pt-5 space-y-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                            सामग्री गुणवत्ता एवं कॉपीराइट दिशानिर्देश (Content Guidelines)
                        </h4>
                        <ul class="text-xs text-slate-500 space-y-1.5 list-disc list-inside leading-relaxed">
                            <li>कृपया केवल सार्वजनिक डोमेन (Public Domain) या वे कृतियां सबमिट करें जिनके वितरण का अधिकार आपके पास है।</li>
                            <li>अपूर्ण, अस्पष्ट या त्रुटिपूर्ण स्कैन की गई पीडीएफ अस्वीकृत की जा सकती हैं।</li>
                            <li>सभी ऑडियो फाइलों में ध्वनि स्पष्ट होनी चाहिए।</li>
                            <li>यदि किसी प्रस्तुति में संशोधन की आवश्यकता होगी, तो संपादकीय टीम आपको समीक्षा टिप्पणी भेजेगी।</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TAB 6: CONTRIBUTOR REWARDS, LEVELS & REFERRALS -->
            <div x-show="currentTab === 'rewards'" style="display: none;" class="space-y-8">
                <!-- Rewards Hero Banner -->
                <div class="rounded-2xl border border-amber-200/90 bg-gradient-to-br from-amber-50/80 via-white to-amber-100/30 p-6 sm:p-8 shadow-xs">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div class="space-y-3 max-w-2xl">
                            <div class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900 border border-amber-200">
                                <x-app-icon name="trophy" class="w-3.5 h-3.5 text-amber-700" />
                                <span>योगदानकर्ता सम्मान एवं स्तर प्रणाली (Library Points & Recognition)</span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 font-serif">
                                साहित्य संवर्धन में आपका योगदान अनमोल है
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                डिजिटल हिन्दी पुस्तकालय पर आपकी प्रत्येक स्वीकृत साहित्यिक कृति के प्रकाशन पर आपको 'लाइब्रेरी अंक' मिलते हैं। ये अंक विशुद्ध रूप से आपकी साहित्यिक गुणवत्ता और सांस्कृतिक सेवा पर आधारित हैं।
                            </p>
                            <div class="inline-flex items-center gap-2 text-xs font-medium text-amber-800 bg-amber-100/70 rounded-xl px-3.5 py-1.5 border border-amber-300/60">
                                <x-app-icon name="shield" class="w-4 h-4 text-amber-700 shrink-0" />
                                <span>100% पारदर्शी, सुरक्षित एवं विशुद्ध साहित्यिक पुरस्कार प्रणाली</span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                            <button 
                                type="button" 
                                @click="openLedgerModal = true"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 shadow-2xs transition cursor-pointer"
                            >
                                <x-app-icon name="file-text" class="w-4 h-4 text-slate-500" />
                                <span>अंक बहीखाता (Ledger)</span>
                            </button>
                            <a 
                                href="{{ route('dashboard.books.create') }}" 
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition cursor-pointer"
                            >
                                <x-app-icon name="plus" class="w-4 h-4" />
                                <span>साहित्य जोड़ें और अंक पाएं</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contributor Tier Progression Roadmap (6 Tiers) -->
                <div class="rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-7 shadow-xs space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-serif">
                                योगदानकर्ता स्तर रोडमैप (Contributor Levels)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                आपके द्वारा प्रकाशित कृतियों की कुल संख्या के आधार पर स्वचालित स्तर उन्नयन।
                            </p>
                        </div>
                        <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200 self-start sm:self-auto">
                            वर्तमान: {{ $user->level_config['label'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach (config('contributor_rewards.levels', []) as $levelKey => $tier)
                            @php
                                $isCurrent = ($user->contributor_level ?: 'reader') === $levelKey;
                                $isUnlocked = $contributorStats['approved_count'] >= $tier['min_approved'];
                            @endphp
                            <div class="rounded-xl border p-4 transition relative flex flex-col justify-between {{ $isCurrent ? 'border-amber-500 bg-amber-50/40 ring-2 ring-amber-500/20 shadow-xs' : ($isUnlocked ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200 bg-slate-50/50 opacity-80') }}">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="h-9 w-9 rounded-xl {{ $isCurrent ? 'bg-amber-600 text-white' : ($isUnlocked ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500') }} flex items-center justify-center">
                                            <x-app-icon :name="$tier['icon'] ?? 'sparkles'" class="w-5 h-5" />
                                        </div>
                                        @if ($isCurrent)
                                            <span class="rounded-full bg-amber-600 text-white text-[10px] font-black uppercase px-2 py-0.5 tracking-wider">
                                                वर्तमान स्तर
                                            </span>
                                        @elseif ($isUnlocked)
                                            <span class="rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 flex items-center gap-1 border border-emerald-200">
                                                <x-app-icon name="check-circle" class="w-3 h-3 text-emerald-600" />
                                                <span>प्राप्त</span>
                                            </span>
                                        @else
                                            <span class="rounded-full bg-slate-100 text-slate-500 text-[10px] font-medium px-2 py-0.5 border border-slate-200">
                                                {{ $tier['min_approved'] }} स्वीकृत कृतियां आवश्यक
                                            </span>
                                        @endif
                                    </div>

                                    <h4 class="text-sm font-bold text-slate-900 font-serif">
                                        {{ $tier['label'] }}
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-1">
                                        न्यूनतम <strong>{{ $tier['min_approved'] }}</strong> प्रकाशित साहित्य कृतियां
                                    </p>
                                </div>

                                <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400">विशेष सम्मान बैज</span>
                                    <span class="font-bold {{ $isCurrent ? 'text-amber-700' : 'text-slate-600' }}">{{ $tier['short_label'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Points Weighting Reference & Safe Monetization Principle -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Literature Weighting Card -->
                    <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-xs space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                                <x-app-icon name="trophy" class="w-5 h-5 text-amber-700" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 font-serif">
                                    साहित्य प्रारूप अनुसार लाइब्रेरी अंक (Points Weighting)
                                </h3>
                                <p class="text-xs text-slate-400">
                                    प्रत्येक स्वीकृत और प्रकाशित रचना पर मिलने वाले अंक
                                </p>
                            </div>
                        </div>

                        <div class="divide-y divide-slate-100 text-xs">
                            <div class="py-2.5 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <x-app-icon name="book-open" class="w-4 h-4 text-amber-700" />
                                    <span class="font-semibold text-slate-800">नई पुस्तक / शोध ग्रंथ (PDF Book)</span>
                                </div>
                                <span class="font-black text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-lg border border-amber-200">+100 अंक</span>
                            </div>
                            <div class="py-2.5 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <x-app-icon name="headphones" class="w-4 h-4 text-violet-700" />
                                    <span class="font-semibold text-slate-800">पूर्ण ऑडियोबुक (Audiobook)</span>
                                </div>
                                <span class="font-black text-violet-700 bg-violet-50 px-2.5 py-0.5 rounded-lg border border-violet-200">+150 अंक</span>
                            </div>
                            <div class="py-2.5 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <x-app-icon name="scroll-text" class="w-4 h-4 text-emerald-700" />
                                    <span class="font-semibold text-slate-800">बाल कथा व लोक कथा श्रृंखला (Stories)</span>
                                </div>
                                <span class="font-black text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-lg border border-emerald-200">+75 अंक</span>
                            </div>
                            <div class="py-2.5 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <x-app-icon name="flame" class="w-4 h-4 text-rose-700" />
                                    <span class="font-semibold text-slate-800">श्री चालीसा व पावन आरती (Devotional)</span>
                                </div>
                                <span class="font-black text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-lg border border-rose-200">+50 अंक</span>
                            </div>
                            <div class="py-2.5 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <x-app-icon name="users" class="w-4 h-4 text-cyan-700" />
                                    <span class="font-semibold text-slate-800">योग्य साहित्यिक रेफरल (Qualified Referral)</span>
                                </div>
                                <span class="font-black text-cyan-700 bg-cyan-50 px-2.5 py-0.5 rounded-lg border border-cyan-200">+100 अंक</span>
                            </div>
                        </div>
                    </div>

                    <!-- Safety & AdSense Compliance Card -->
                    <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/30 p-6 shadow-xs space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center">
                                <x-app-icon name="shield" class="w-5 h-5 text-emerald-700" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 font-serif">
                                    विज्ञापनों से पूर्णतः पृथक एवं नैतिक नीति (AdSense Safe)
                                </h3>
                                <p class="text-xs text-slate-500">
                                    शुद्ध साहित्यिक गुणवत्ता, शून्य कृत्रिम ट्रैफ़िक
                                </p>
                            </div>
                        </div>

                        <div class="text-xs text-slate-600 space-y-2 leading-relaxed">
                            <p>
                                <strong>1. ट्रैफ़िक या क्लिक पर कोई अंक नहीं:</strong> वेबसाइट पर विज्ञापनों (Google AdSense), पेजव्यू, विज़िट या सर्च पर कभी भी कोई अंक या प्रोत्साहन नहीं दिया जाता।
                            </p>
                            <p>
                                <strong>2. केवल अनुमोदित साहित्य:</strong> अंक केवल तभी जुड़ते हैं जब संपादकीय मंडल आपकी सामग्री की प्रमाणिकता, कॉपीराइट और गुणवत्ता को स्वीकृत करता है।
                            </p>
                            <p>
                                <strong>3. प्रतिवर्ती लेखा (Reversible Ledger):</strong> यदि कोई सामग्री वापस ली जाती है या हटाई जाती है, तो उसके अंक बहीखाते में स्वतः समायोजित हो जाते हैं।
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contributor Organic Referral Program -->
                <div class="rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-7 shadow-xs space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                        <div>
                            <div class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-amber-700 mb-1">
                                <x-app-icon name="users" class="w-4 h-4" />
                                <span>मित्रों एवं साहित्यकारों को आमंत्रित करें (Invite Contributors)</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 font-serif">
                                आपका विशेष योगदानकर्ता आमंत्रण लिंक
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                साथी लेखकों व शोधकर्ताओं को आमंत्रित करें। जब उनकी पहली रचना स्वीकृत होकर प्रकाशित होगी, आपको +100 लाइब्रेरी अंक मिलेंगे।
                            </p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="text-xs text-slate-400">कोड:</span>
                            <span class="font-mono text-sm font-black bg-amber-50 text-amber-900 border border-amber-200 px-3 py-1 rounded-xl tracking-wider">
                                {{ $user->referral_code }}
                            </span>
                        </div>
                    </div>

                    <!-- Link Copy Box & Social Invite -->
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row items-stretch gap-2.5">
                            <input 
                                type="text" 
                                readonly 
                                value="{{ route('register', ['ref' => $user->referral_code]) }}" 
                                class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-700 focus:outline-none select-all"
                            />
                            <button 
                                type="button" 
                                @click="copyText('{{ route('register', ['ref' => $user->referral_code]) }}')"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 px-5 py-2.5 text-xs font-bold text-white shrink-0 transition cursor-pointer shadow-xs"
                            >
                                <x-app-icon name="clipboard" class="w-4 h-4" />
                                <span x-text="copySuccess ? 'कॉपी हो गया! ✓' : 'लिंक कॉपी करें'">लिंक कॉपी करें</span>
                            </button>
                            <a 
                                href="https://wa.me/?text={{ urlencode('नमस्ते! डिजिटल हिन्दी पुस्तकालय पर साहित्य, पुस्तकें एवं कथाएं पढ़ने और योगदान करने के लिए मेरे आमंत्रण लिंक से जुड़ें: ' . route('register', ['ref' => $user->referral_code])) }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white shrink-0 transition cursor-pointer shadow-xs"
                            >
                                <x-app-icon name="share-2" class="w-4 h-4" />
                                <span>WhatsApp पर भेजें</span>
                            </a>
                        </div>

                        <!-- 4 Referral KPI Metrics -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                            <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5 text-center">
                                <div class="text-[11px] font-semibold text-slate-500 uppercase">कुल आमंत्रित</div>
                                <div class="text-xl font-bold text-slate-900 mt-0.5 font-serif">{{ $contributorStats['referral_stats']['total_invited'] }}</div>
                            </div>
                            <div class="rounded-xl border border-slate-100 bg-amber-50/50 p-3.5 text-center">
                                <div class="text-[11px] font-semibold text-amber-800 uppercase">समीक्षाधीन</div>
                                <div class="text-xl font-bold text-amber-700 mt-0.5 font-serif">{{ $contributorStats['referral_stats']['pending'] }}</div>
                            </div>
                            <div class="rounded-xl border border-slate-100 bg-emerald-50/50 p-3.5 text-center">
                                <div class="text-[11px] font-semibold text-emerald-800 uppercase">सफल / प्रकाशित</div>
                                <div class="text-xl font-bold text-emerald-700 mt-0.5 font-serif">{{ $contributorStats['referral_stats']['qualified'] }}</div>
                            </div>
                            <div class="rounded-xl border border-slate-100 bg-cyan-50/50 p-3.5 text-center">
                                <div class="text-[11px] font-semibold text-cyan-800 uppercase">अर्जित अंक</div>
                                <div class="text-xl font-bold text-cyan-700 mt-0.5 font-serif">+{{ number_format($contributorStats['referral_stats']['points_earned']) }}</div>
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-400 italic">
                            * रेफरल प्रोत्साहन केवल साहित्यिक योगदान के लिए है। साइन-अप, विज़िट या विज्ञापनों पर कोई अंक नहीं दिए जाते।
                        </p>
                    </div>
                </div>

                <!-- Points Ledger Audit History Table -->
                <div class="rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-7 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-serif">
                                अंक बहीखाता विवरण (Points Audit Ledger)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                अपरिवर्तनीय एवं सुरक्षित वित्तीय-स्तरीय ऑडिट रिकॉर्ड (Append-Only Ledger)
                            </p>
                        </div>
                        <span class="text-xs font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                            कुल: {{ number_format($user->total_points) }} अंक
                        </span>
                    </div>

                    @if ($pointsLedger->isNotEmpty())
                        <div class="overflow-x-auto border border-slate-200/80 rounded-xl">
                            <table class="w-full text-left text-xs divide-y divide-slate-200">
                                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                                    <tr>
                                        <th class="px-4 py-3">दिनांक</th>
                                        <th class="px-4 py-3">विवरण / साहित्य</th>
                                        <th class="px-4 py-3">गतिविधि</th>
                                        <th class="px-4 py-3 text-right">अंक (+/-)</th>
                                        <th class="px-4 py-3 text-center">सत्यापन</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($pointsLedger as $entry)
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="px-4 py-3 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                                {{ $entry->created_at->format('d M Y, h:i A') }}
                                            </td>
                                            <td class="px-4 py-3 font-medium text-slate-900">
                                                {{ $entry->description }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $entry->points >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                                    {{ str_replace('_', ' ', $entry->event_type) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right font-black text-sm font-serif {{ $entry->points >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $entry->points > 0 ? '+' : '' }}{{ $entry->points }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                                    <x-app-icon name="check-circle" class="w-3 h-3 text-emerald-600" />
                                                    <span>Verified</span>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/50 p-8 text-center text-xs text-slate-500">
                            <p class="font-medium text-slate-700">अभी तक कोई अंक प्रविष्टि नहीं है।</p>
                            <p class="mt-1 text-slate-400">अपनी पुस्तकें, ऑडियोबुक या कथाएं सबमिट करें — संपादकीय स्वीकृति के तुरंत बाद आपके अंक यहां दर्ज होंगे।</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- MODAL 1: AUDITABLE POINTS LEDGER MODAL -->
        <div 
            x-show="openLedgerModal" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;" 
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        >
            <div 
                @click.away="openLedgerModal = false"
                class="relative w-full max-w-3xl rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-xs">
                            <x-app-icon name="trophy" class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 font-serif">
                                अंक बहीखाता (Auditable Points Ledger)
                            </h3>
                            <p class="text-xs text-slate-400">
                                कुल संचित अंक: <strong class="text-amber-700">{{ number_format($user->total_points) }} अंक</strong> • अपरिवर्तनीय वित्तीय स्तर रिकॉर्ड
                            </p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="openLedgerModal = false"
                        class="text-slate-400 hover:text-slate-700 text-xl font-bold p-1 cursor-pointer"
                    >
                        &times;
                    </button>
                </div>

                <div class="max-h-96 overflow-y-auto border border-slate-200 rounded-xl">
                    @if ($pointsLedger->isNotEmpty())
                        <table class="w-full text-left text-xs divide-y divide-slate-200">
                            <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[11px] sticky top-0">
                                <tr>
                                    <th class="px-4 py-2.5">समय</th>
                                    <th class="px-4 py-2.5">विवरण</th>
                                    <th class="px-4 py-2.5 text-right">अंक</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($pointsLedger as $entry)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-2.5 whitespace-nowrap text-slate-400 text-[11px] font-mono">
                                            {{ $entry->created_at->format('d M Y, h:i A') }}
                                        </td>
                                        <td class="px-4 py-2.5 text-slate-900">
                                            {{ $entry->description }}
                                        </td>
                                        <td class="px-4 py-2.5 text-right whitespace-nowrap font-black font-serif {{ $entry->points >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $entry->points > 0 ? '+' : '' }}{{ $entry->points }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-8 text-center text-xs text-slate-500">
                            अभी तक कोई प्रविष्टि नहीं है।
                        </div>
                    @endif
                </div>

                <div class="rounded-xl bg-slate-50 p-3 text-[11px] text-slate-500 flex items-center justify-between border border-slate-200">
                    <span class="flex items-center gap-1.5">
                        <x-app-icon name="shield" class="w-3.5 h-3.5 text-emerald-600" />
                        <span>SHA-256 Idempotency Protected • Append-Only Database Guarantee</span>
                    </span>
                    <button 
                        type="button" 
                        @click="openLedgerModal = false"
                        class="rounded-lg bg-slate-900 hover:bg-slate-800 text-white px-3.5 py-1.5 font-bold cursor-pointer transition"
                    >
                        बंद करें
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL 2: ORGANIC CONTENT SOCIAL SHARE MODAL -->
        <div 
            x-show="openShareModal" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;" 
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        >
            <div 
                @click.away="openShareModal = false"
                class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-slate-200 space-y-5"
            >
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="h-9 w-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center">
                            <x-app-icon name="share-2" class="w-5 h-5 text-amber-700" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 font-serif">
                                साहित्य साझा करें (Organic Share)
                            </h3>
                            <p class="text-[11px] text-slate-400 truncate max-w-xs" x-text="shareTitle"></p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="openShareModal = false"
                        class="text-slate-400 hover:text-slate-700 text-xl font-bold p-1 cursor-pointer"
                    >
                        &times;
                    </button>
                </div>

                <div class="space-y-4">
                    <p class="text-xs text-slate-600 leading-relaxed">
                        अपनी प्रकाशित साहित्यिक कृति को मित्रों और पाठकों के साथ साझा करें ताकि अधिक से अधिक लोग ज्ञान और संस्कृति से लाभान्वित हो सकें।
                    </p>

                    <div class="grid grid-cols-3 gap-3">
                        <!-- WhatsApp -->
                        <a 
                            :href="'https://wa.me/?text=' + encodeURIComponent(shareTitle + ' पढ़ें या सुनें डिजिटल हिन्दी पुस्तकालय पर: ' + shareUrl)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition"
                        >
                            <x-app-icon name="share-2" class="w-4 h-4 text-emerald-600" />
                            <span>WhatsApp</span>
                        </a>

                        <!-- Twitter / X -->
                        <a 
                            :href="'https://twitter.com/intent/tweet?text=' + encodeURIComponent(shareTitle + ' @hindipdflibrary') + '&url=' + encodeURIComponent(shareUrl)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-800 text-xs font-bold transition"
                        >
                            <x-app-icon name="external-link" class="w-4 h-4 text-slate-600" />
                            <span>Twitter / X</span>
                        </a>

                        <!-- Facebook -->
                        <a 
                            :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(shareUrl)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex flex-col items-center justify-center gap-1.5 p-3 rounded-xl border border-blue-200 bg-blue-50/50 hover:bg-blue-100 text-blue-800 text-xs font-bold transition"
                        >
                            <x-app-icon name="external-link" class="w-4 h-4 text-blue-600" />
                            <span>Facebook</span>
                        </a>
                    </div>

                    <!-- Direct Copy -->
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-semibold text-slate-500 uppercase">सीधा वेब लिंक (Direct Link)</label>
                        <div class="flex items-center gap-2">
                            <input 
                                type="text" 
                                readonly 
                                :value="shareUrl" 
                                class="w-full text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-slate-700 select-all focus:outline-none"
                            />
                            <button 
                                type="button" 
                                @click="copyText(shareUrl)"
                                class="inline-flex items-center gap-1 rounded-xl bg-slate-900 hover:bg-slate-800 text-white px-3.5 py-2 text-xs font-bold shrink-0 transition cursor-pointer"
                            >
                                <x-app-icon name="clipboard" class="w-3.5 h-3.5" />
                                <span x-text="copySuccess ? 'कॉपी! ✓' : 'कॉपी'">कॉपी</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex justify-end">
                    <button 
                        type="button" 
                        @click="openShareModal = false"
                        class="rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-700 cursor-pointer"
                    >
                        पूर्ण (Done)
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
