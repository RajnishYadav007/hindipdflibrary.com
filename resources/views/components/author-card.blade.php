@props([
    'author' => null,
    'name' => null,
    'photoUrl' => null,
    'bookCount' => null,
    'bio' => null,
    'url' => '#',
])

@php
    if ($author) {
        $name = $name ?? ($author->translations->where('locale', 'hi')->first()?->name 
                ?? $author->translations->where('locale', 'en')->first()?->name 
                ?? $author->slug);
        
        $bio = $bio ?? ($author->translations->where('locale', 'hi')->first()?->biography 
               ?? $author->translations->where('locale', 'en')->first()?->biography);
        
        $bookCount = $bookCount ?? $author->books_count ?? ($author->books ? $author->books->count() : null);
        $url = $url !== '#' ? $url : url('/authors/' . $author->slug);
    }

    $initials = collect(explode(' ', $name ?? 'Author'))
        ->map(fn ($segment) => mb_substr($segment, 0, 1))
        ->take(2)
        ->join('');
@endphp

<div class="group flex items-start gap-3.5 rounded-xl border border-slate-200 bg-white p-4 shadow-2xs transition-all hover:border-brand-300 hover:shadow-xs">
    <!-- Avatar / Photo -->
    <div class="relative h-12 w-12 shrink-0 overflow-hidden rounded-full bg-amber-100 border border-amber-200 text-brand-800 flex items-center justify-center font-bold text-sm">
        @if ($photoUrl)
            <img src="{{ $photoUrl }}" alt="Photo of {{ $name }}" class="h-full w-full object-cover" loading="lazy" />
        @else
            <span>{{ $initials }}</span>
        @endif
    </div>

    <!-- Details -->
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <h3 class="text-sm font-bold text-slate-900 group-hover:text-brand-800 transition truncate">
                <a href="{{ $url }}" class="focus-visible:outline-brand-700">
                    {{ $name }}
                </a>
            </h3>

            @if ($bookCount !== null)
                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 shrink-0">
                    {{ $bookCount }} {{ $bookCount === 1 ? 'Book' : 'Books' }}
                </span>
            @endif
        </div>

        @if ($bio)
            <p class="mt-1 text-xs text-slate-500 clamp-2 leading-relaxed">
                {{ $bio }}
            </p>
        @endif
    </div>
</div>
