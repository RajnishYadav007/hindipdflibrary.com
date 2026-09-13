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
        $title = $title ?? ($book->translations->where('locale', 'hi')->first()?->title 
                 ?? $book->translations->where('locale', 'en')->first()?->title 
                 ?? $book->slug);
        
        $author = $author ?? $book->authors->first()?->translations->where('locale', 'hi')->first()?->name 
                  ?? $book->authors->first()?->translations->where('locale', 'en')->first()?->name 
                  ?? $book->authors->first()?->slug;
        
        $category = $category ?? $book->categories->first()?->translations->where('locale', 'hi')->first()?->name 
                    ?? $book->categories->first()?->slug;
        
        $coverMedia = $book->media?->where('collection_name', 'cover')->first() ?? $book->media?->first();
        $coverUrl = $coverUrl ?? ($coverMedia ? asset('storage/' . $coverMedia->file_name) : null);
        $year = $year ?? $book->publication_year;
        $rating = $rating ?? $book->rating_average;
        $ratingCount = $ratingCount ?? $book->rating_count;
        $url = $url !== '#' ? $url : route('books.show', $book->slug);
    }
@endphp

<article class="group relative flex flex-col rounded-xl border border-slate-200 bg-white p-3 shadow-book transition-all duration-200 hover:-translate-y-1 hover:shadow-book-hover">
    <!-- Book Cover Container (Fixed 2:3 Aspect Ratio) -->
    <div class="relative w-full aspect-[2/3] overflow-hidden rounded-lg bg-gradient-to-b from-slate-100 to-slate-200 shadow-inner">
        @if ($coverUrl)
            <img 
                src="{{ $coverUrl }}" 
                alt="Cover of {{ $title }}" 
                loading="lazy"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-103"
            />
        @else
            <!-- Elegant Fallback Book Cover -->
            <div class="flex h-full w-full flex-col justify-between p-4 text-center bg-gradient-to-br from-amber-800 to-amber-950 text-amber-50">
                <div class="flex items-center justify-between opacity-60 text-[10px] tracking-widest uppercase">
                    <span>{{ $language ? strtoupper($language) : 'HINDI' }}</span>
                    <span>PDF</span>
                </div>
                
                <div class="my-auto px-2">
                    <div class="mx-auto mb-2 flex h-8 w-8 items-center justify-center rounded-full bg-white/10">
                        <svg class="h-4 w-4 text-amber-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <p class="font-serif text-sm font-bold leading-tight clamp-3 text-amber-100 drop-shadow-xs">
                        {{ $title }}
                    </p>
                    @if ($author)
                        <p class="mt-1 text-[11px] text-amber-200/80 clamp-1">
                            {{ $author }}
                        </p>
                    @endif
                </div>

                <div class="text-[9px] tracking-wider opacity-60 uppercase">
                    Hindi PDF Library
                </div>
            </div>
        @endif

        <!-- Favorite Button Overlay -->
        <div class="absolute top-2 right-2 z-10">
            <x-favorite-button :isFavorited="$isFavorited" size="sm" />
        </div>

        <!-- Language Pill Badge -->
        @if ($language)
            <div class="absolute bottom-2 left-2 z-10">
                <span class="rounded bg-slate-900/80 backdrop-blur-xs px-1.5 py-0.5 text-[10px] font-semibold text-white uppercase tracking-wider">
                    {{ $language }}
                </span>
            </div>
        @endif
    </div>

    <!-- Metadata Section -->
    <div class="flex flex-1 flex-col justify-between pt-3">
        <div>
            @if ($category)
                <span class="text-[11px] font-medium text-brand-700 tracking-wide uppercase">
                    {{ $category }}
                </span>
            @endif

            <!-- Title Link -->
            <h3 class="mt-1 text-sm font-bold text-slate-900 leading-snug group-hover:text-brand-800 transition clamp-2">
                <a href="{{ $url }}" class="focus-visible:outline-brand-700 rounded-xs">
                    {{ $title }}
                </a>
            </h3>

            <!-- Author -->
            @if ($author)
                <p class="mt-1 text-xs text-slate-600 clamp-1">
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
