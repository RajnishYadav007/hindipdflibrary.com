@props([
    'book' => null,
    'title' => null,
    'author' => null,
    'coverUrl' => null,
    'url' => '#',
    'language' => 'hi',
    'category' => null,
    'rating' => 0,
    'ratingCount' => null,
    'year' => null,
    'isFavorited' => false,
])

@php
    // Extract properties if an Eloquent Book model is passed
    if ($book) {
        $locale = app()->getLocale();
        $title = $title ?? ($book->localized($locale)?->title ?? $book->slug);
        
        $primaryAuthor = $book->authors->first();
        $author = $author ?? ($primaryAuthor?->localized($locale)?->name ?? $primaryAuthor?->slug);
        
        $primaryCategory = $book->categories->first();
        $category = $category ?? ($primaryCategory?->localized($locale)?->name ?? $primaryCategory?->slug);
        
        $coverUrl = $coverUrl ?? $book->cover_url;
        $year = $year ?? $book->publication_year;
        $pages = $book->pages;
        $description = $book->description ?: ($title . ' by ' . $author);
        $rating = $rating ?? $book->rating_average;
        $ratingCount = $ratingCount ?? $book->rating_count;
        $url = $url !== '#' ? $url : route('books.show', ['slug' => $book->slug]);
        $downloadUrl = route('books.download', ['slug' => $book->slug]);
        $bookId = $book->id ?? 1;
    } else {
        $description = $title;
        $pages = null;
        $downloadUrl = $url;
        $bookId = 1;
    }

    // Check if this book has a genuine uploaded/hosted image vs an archive.org generic placeholder
    $hasRealCover = !empty($coverUrl) && !str_contains($coverUrl, 'services/img/');

    // Luxury cloth-bound hardcover palettes
    $themes = [
        ['from' => 'from-rose-950', 'via' => 'via-red-900', 'to' => 'to-amber-950', 'accent' => 'text-amber-200', 'border' => 'border-amber-400/40', 'tag' => 'कालजयी काव्य'],
        ['from' => 'from-emerald-950', 'via' => 'via-teal-900', 'to' => 'to-slate-900', 'accent' => 'text-emerald-200', 'border' => 'border-emerald-400/40', 'tag' => 'नीतिकथा व ज्ञान'],
        ['from' => 'from-indigo-950', 'via' => 'via-slate-900', 'to' => 'to-blue-950', 'accent' => 'text-sky-200', 'border' => 'border-sky-400/40', 'tag' => 'साहित्यिक क्लासिक'],
        ['from' => 'from-amber-950', 'via' => 'via-yellow-950', 'to' => 'to-stone-900', 'accent' => 'text-amber-300', 'border' => 'border-amber-500/40', 'tag' => 'वैदिक व पौराणिक'],
        ['from' => 'from-slate-950', 'via' => 'via-neutral-900', 'to' => 'to-zinc-950', 'accent' => 'text-amber-100', 'border' => 'border-amber-300/30', 'tag' => 'डिजिटल ग्रंथावली'],
    ];
    $theme = $themes[abs(($bookId ?? crc32($title)) % count($themes))];
@endphp

<article class="group relative flex flex-col rounded-[22px] border border-slate-200/90 bg-white p-3.5 shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:shadow-md hover:border-sky-300">
    <!-- Book Cover Container (Fixed 2:3 Aspect Ratio) -->
    <div class="relative w-full aspect-[2/3] overflow-hidden rounded-2xl bg-slate-900 shadow-inner">
        @if ($hasRealCover)
            <img 
                src="{{ $coverUrl }}" 
                alt="{{ $title }} - {{ $author ? $author . ' | ' : '' }}हिन्दी पुस्तक कवर (Hindi PDF Book Cover)" 
                title="{{ $title }} - {{ $author ? $author . ' | ' : '' }}हिन्दी पुस्तक"
                loading="lazy"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-103"
                onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
            />
        @endif

        <!-- Luxury Cloth-Bound Hardcover Fallback (Rendered when no real cover exists or on image error) -->
        <div class="{{ $hasRealCover ? 'hidden' : 'flex' }} h-full w-full bg-gradient-to-b {{ $theme['from'] }} {{ $theme['via'] }} {{ $theme['to'] }} p-2.5 relative flex-col justify-between overflow-hidden shadow-[inset_5px_0_10px_rgba(0,0,0,0.6)] select-none">
            <!-- Spine Highlight Curve -->
            <div class="absolute left-0 top-0 bottom-0 w-2.5 bg-gradient-to-r from-white/20 via-white/5 to-transparent border-r border-white/10 pointer-events-none"></div>

            <!-- Delicate Gold Foil Frame -->
            <div class="h-full w-full border {{ $theme['border'] }} rounded-xl p-2.5 flex flex-col justify-between relative">
                <!-- Top Brand & Tag -->
                <div class="flex items-center justify-between opacity-80 text-[7.5px] tracking-wider uppercase font-semibold text-amber-200">
                    <span>Hindi PDF Library</span>
                    <span class="{{ $theme['accent'] }}">{{ $theme['tag'] }}</span>
                </div>
                
                <!-- Center Title & Emblem -->
                <div class="my-auto text-center px-1">
                    <div class="mx-auto mb-2 flex h-7 w-7 items-center justify-center rounded-full bg-white/10 text-amber-200 shadow-xs">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    
                    <h4 class="font-serif text-xs sm:text-sm font-extrabold leading-snug line-clamp-3 text-white drop-shadow-sm">
                        {{ $title }}
                    </h4>
                    
                    @if ($author)
                        <p class="mt-1.5 text-[10px] font-medium {{ $theme['accent'] }} line-clamp-1 italic">
                            — {{ $author }} —
                        </p>
                    @endif
                </div>

                <!-- Bottom Edition Stamp -->
                <div class="text-[7.5px] tracking-widest text-center opacity-70 uppercase text-slate-300">
                    COLLECTOR'S EDITION
                </div>
            </div>
        </div>

        <!-- Favorite Button Overlay -->
        <div class="absolute top-2 right-2 z-10">
            <x-favorite-button :isFavorited="$isFavorited" size="sm" />
        </div>

        <!-- Language Pill Badge -->
        @if ($language)
            <div class="absolute bottom-2 left-2 z-10">
                <span class="rounded-full bg-slate-900/80 backdrop-blur-md px-2 py-0.5 text-[9px] font-semibold text-white uppercase tracking-wider shadow-xs">
                    {{ $language }}
                </span>
            </div>
        @endif

        <!-- Quick Audio & Drawer Actions -->
        <div class="absolute bottom-2 right-2 z-10 flex items-center gap-1.5 opacity-90 sm:opacity-0 sm:group-hover:opacity-100 transition duration-200">
            <!-- Audio TTS Button -->
            <button 
                type="button" 
                @click.stop="window.dispatchEvent(new CustomEvent('play-book-audio', { detail: { title: '{{ addslashes($title) }}', author: '{{ addslashes($author ?? '') }}', text: '{{ addslashes(preg_replace('/\s+/', ' ', $description)) }}' } }))"
                class="flex h-7 w-7 items-center justify-center rounded-full bg-[#48b3e8] hover:bg-[#32a2db] text-white shadow-xs transition hover:scale-110 active:scale-95"
                title="Listen to Book (ऑडियो सुनें)"
                aria-label="Listen to Book"
            >
                <svg class="h-3.5 w-3.5 fill-current ml-0.5" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z" />
                </svg>
            </button>

            <!-- Info Drawer Trigger -->
            <button 
                type="button" 
                @click.stop="window.dispatchEvent(new CustomEvent('open-book-drawer', { detail: { title: '{{ addslashes($title) }}', author: '{{ addslashes($author ?? '') }}', description: '{{ addslashes(preg_replace('/\s+/', ' ', $description)) }}', coverUrl: '{{ $coverUrl }}', pages: '{{ $pages }}', rating: '{{ $rating }}', reviewsCount: '{{ $ratingCount }}', url: '{{ $url }}', downloadUrl: '{{ $downloadUrl }}' } }))"
                class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-900/80 hover:bg-slate-800 text-white backdrop-blur-xs transition shadow-xs hover:scale-110"
                title="Quick Preview (त्वरित झलक)"
                aria-label="Quick Preview"
            >
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Metadata Section -->
    <div class="flex flex-1 flex-col justify-between pt-3">
        <div>
            @if ($category)
                <span class="text-[10px] font-bold text-[#48b3e8] tracking-wider uppercase">
                    {{ $category }}
                </span>
            @endif

            <!-- Title Link -->
            <h3 class="mt-0.5 text-sm font-bold text-slate-900 leading-snug group-hover:text-[#48b3e8] transition line-clamp-2">
                <a href="{{ $url }}" class="focus-visible:outline-[#48b3e8] rounded-xs">
                    {{ $title }}
                </a>
            </h3>

            <!-- Author -->
            @if ($author)
                <p class="mt-1 text-xs text-slate-500 line-clamp-1">
                    {{ $author }}
                </p>
            @endif
        </div>

        <!-- Footer Bar (Rating & Year) -->
        <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-100 text-xs text-slate-500">
            <x-rating-stars :rating="$rating" :ratingCount="$ratingCount" size="xs" />

            @if ($year)
                <span class="text-[11px] text-slate-400 font-medium">
                    {{ $year }}
                </span>
            @endif
        </div>
    </div>
</article>