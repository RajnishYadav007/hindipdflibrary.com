@props([
    'book' => null,
])

@if ($book)
    @php
        $title = $book->title;
        $author = $book->authors->first()?->name ?? 'Munshi Premchand';
        $category = $book->categories->first()?->name ?? 'Hindi Literature';
        $coverUrl = $book->cover_url;
        $description = $book->description ?: 'एक कालजयी हिन्दी साहित्यिक कृति। इसके पात्र, सामाजिक यथार्थ और भाषा शैली भारतीय जीवन दर्शन का सजीव चित्रण करते हैं।';
    @endphp

    <div class="relative overflow-hidden rounded-[28px] bg-gradient-to-r from-[#2c2018] via-[#43342a] to-[#735541] text-white p-6 sm:p-10 shadow-xl border border-[#523e31]/80">
        <!-- Soft organic glow effect -->
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-[#8c654b]/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 h-64 w-64 rounded-full bg-[#3d2c20]/40 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
            <!-- Book Cover -->
            <div class="shrink-0 w-44 sm:w-52 aspect-[2/3] rounded-2xl overflow-hidden shadow-2xl border-2 border-white/20 bg-slate-900 relative group">
                @if ($coverUrl)
                    <img src="{{ $coverUrl }}" alt="{{ $title }}" class="h-full w-full object-cover group-hover:scale-102 transition duration-300" />
                @else
                    <div class="h-full w-full flex flex-col items-center justify-center p-4 text-center bg-gradient-to-b from-[#241a14] to-[#17110d]">
                        <span class="text-3xl font-extrabold text-amber-200">📚</span>
                        <span class="mt-2 text-xs font-bold text-white line-clamp-3">{{ $title }}</span>
                        <span class="mt-1 text-[10px] text-amber-200/70">{{ $author }}</span>
                    </div>
                @endif
                <div class="absolute top-2.5 left-2.5">
                    <span class="inline-flex items-center gap-1 rounded-full bg-black/60 backdrop-blur-md px-2.5 py-0.5 text-[10px] font-bold text-amber-300 border border-amber-400/30 shadow-xs">
                        🔥 Trending Now
                    </span>
                </div>
            </div>

            <!-- Details & Actions -->
            <div class="flex-1 min-w-0 space-y-4 text-center md:text-left">
                <!-- Badges -->
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                    <span class="inline-flex items-center rounded-full bg-white/15 backdrop-blur-md px-3 py-1 text-xs font-semibold text-amber-100 border border-white/10">
                        {{ $category }}
                    </span>
                    @if ($book->publication_year)
                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-slate-300 border border-white/5">
                            {{ $book->publication_year }}
                        </span>
                    @endif
                    @if ($book->pages)
                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-slate-300 border border-white/5">
                            {{ $book->pages }} Pages
                        </span>
                    @endif
                    @if ($book->canDownloadPdf())
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 px-3 py-1 text-xs font-medium text-emerald-200">
                            ✓ Public Domain
                        </span>
                    @endif
                </div>

                <!-- Title & Author -->
                <div>
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight font-serif">
                        <a href="{{ route('books.show', $book->slug) }}" class="hover:text-amber-200 transition">
                            {{ $title }}
                        </a>
                    </h2>
                    <p class="mt-1.5 text-sm sm:text-base font-medium text-amber-100/90">
                        By <span class="text-white font-bold">{{ $author }}</span>
                    </p>
                </div>

                <!-- Rating -->
                @if ($book->rating_average)
                    <div class="flex items-center justify-center md:justify-start gap-2 text-xs text-amber-300">
                        <div class="flex text-amber-400">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="h-4 w-4 {{ $i <= round($book->rating_average) ? 'fill-current' : 'text-white/20' }}" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        <span class="font-bold text-sm text-white">{{ number_format($book->rating_average, 1) }}</span>
                        <span class="text-amber-100/70">({{ $book->rating_count ?? 0 }} readers)</span>
                    </div>
                @endif

                <!-- Description -->
                <p class="text-xs sm:text-sm text-slate-200/90 line-clamp-3 max-w-2xl leading-relaxed">
                    {{ $description }}
                </p>

                <!-- Action Buttons Row (Matching Image 2 Booknest style) -->
                <div class="pt-3 flex flex-wrap items-center justify-center md:justify-start gap-3">
                    <!-- Primary Read Button (Crisp White Pill CTA from Image 2) -->
                    <a 
                        href="{{ route('books.show', $book->slug) }}" 
                        class="inline-flex items-center gap-2 rounded-full bg-white px-7 py-3 text-xs font-bold text-slate-900 hover:bg-slate-100 shadow-lg shadow-black/20 transition hover:scale-102 active:scale-98"
                    >
                        <span>Read the book</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    <!-- Listen Round/Pill Button -->
                    <button 
                        type="button" 
                        @click="window.dispatchEvent(new CustomEvent('play-book-audio', { detail: { title: '{{ addslashes($title) }}', author: '{{ addslashes($author) }}', text: '{{ addslashes(preg_replace('/\s+/', ' ', $description)) }}' } }))"
                        class="inline-flex items-center gap-2 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 px-4 py-2.5 text-xs font-semibold text-white transition hover:scale-105"
                        title="Listen to Book (ऑडियो सुनें)"
                    >
                        <svg class="h-4 w-4 fill-current text-sky-300" viewBox="0 0 24 24">
                            <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z" />
                        </svg>
                        <span>Listen</span>
                    </button>

                    <!-- Favorite Button (Circular Glass Button) -->
                    <form action="{{ route('favorites.toggle', $book) }}" method="POST" class="inline">
                        @csrf
                        <button 
                            type="submit" 
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 hover:bg-white/20 border border-white/20 text-white transition hover:scale-105"
                            title="Add to Favorites"
                            aria-label="Add to Favorites"
                        >
                            <svg class="h-4 w-4 fill-current text-rose-400" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </button>
                    </form>

                    <!-- Download PDF or Source Link if permitted by Legal status -->
                    @if ($book->canDownloadPdf())
                        <a 
                            href="{{ route('books.download', $book->slug) }}" 
                            class="inline-flex items-center gap-1.5 rounded-full bg-[#48b3e8]/20 hover:bg-[#48b3e8]/30 border border-[#48b3e8]/40 px-4 py-2.5 text-xs font-semibold text-sky-200 transition hover:scale-102"
                            title="Download PDF"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <span>PDF</span>
                        </a>
                    @elseif ($book->getSafeSourceUrl())
                        <a 
                            href="{{ $book->getSafeSourceUrl() }}" 
                            target="_blank" 
                            rel="noopener noreferrer nofollow"
                            class="inline-flex items-center gap-1.5 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 px-4 py-2.5 text-xs font-semibold text-slate-200 transition"
                        >
                            <span>Archive</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
