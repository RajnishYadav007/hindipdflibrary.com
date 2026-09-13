@props([
    'book' => null,
    'metadata' => [],
])

@php
    $items = $metadata;

    if ($book) {
        if ($book->authors && $book->authors->isNotEmpty()) {
            $items['Author'] = $book->authors->map(fn ($a) => 
                $a->translations->where('locale', 'hi')->first()?->name 
                ?? $a->translations->where('locale', 'en')->first()?->name 
                ?? $a->slug
            )->join(', ');
        }

        if ($book->categories && $book->categories->isNotEmpty()) {
            $items['Category'] = $book->categories->map(fn ($c) => 
                $c->translations->where('locale', 'hi')->first()?->name 
                ?? $c->slug
            )->join(', ');
        }

        if ($book->genres && $book->genres->isNotEmpty()) {
            $items['Genre'] = $book->genres->map(fn ($g) => 
                $g->translations->where('locale', 'hi')->first()?->name 
                ?? $g->slug
            )->join(', ');
        }

        if ($book->originalLanguage) {
            $items['Language'] = $book->originalLanguage->native_name ?? $book->originalLanguage->name;
        }

        if (!empty($book->publisher)) {
            $items['Publisher'] = $book->publisher;
        }

        if (!empty($book->publication_year)) {
            $items['Publication Year'] = $book->publication_year;
        }

        if (!empty($book->edition)) {
            $items['Edition'] = $book->edition;
        }

        if (!empty($book->pages)) {
            $items['Total Pages'] = $book->pages . ' pages';
        }

        if (!empty($book->isbn)) {
            $items['ISBN'] = $book->isbn;
        }

        if (!empty($book->isbn13)) {
            $items['ISBN-13'] = $book->isbn13;
        }

        if (!empty($book->license_type)) {
            $items['License & Rights'] = $book->license_type;
        }

        if (!empty($book->rights_holder)) {
            $items['Rights Holder'] = $book->rights_holder;
        }
    }
@endphp

@if (!empty($items))
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white p-5 shadow-2xs']) }}>
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
            <svg class="h-4 w-4 text-brand-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            Book Specifications & Details
        </h3>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-xs">
            @foreach ($items as $label => $value)
                @if (!empty($value))
                    <div class="flex flex-col py-1 border-b border-slate-50 last:border-0 sm:last:border-0">
                        <dt class="font-medium text-slate-400 uppercase tracking-wide text-[10px]">{{ $label }}</dt>
                        <dd class="mt-0.5 font-semibold text-slate-800 text-sm leading-snug">{{ $value }}</dd>
                    </div>
                @endif
            @endforeach
        </dl>
    </div>
@endif
