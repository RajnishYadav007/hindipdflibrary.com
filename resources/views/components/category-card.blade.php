@props([
    'category' => null,
    'name' => null,
    'description' => null,
    'bookCount' => null,
    'url' => '#',
])

@php
    if ($category) {
        $name = $name ?? ($category->translations->where('locale', 'hi')->first()?->name 
                ?? $category->translations->where('locale', 'en')->first()?->name 
                ?? $category->slug);
        
        $description = $description ?? ($category->translations->where('locale', 'hi')->first()?->description 
                       ?? $category->translations->where('locale', 'en')->first()?->description);
        
        $bookCount = $bookCount ?? $category->books_count ?? ($category->books ? $category->books->count() : null);
        $url = $url !== '#' ? $url : route('categories.show', $category->slug);
    }
@endphp

<div class="group flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-4 shadow-2xs transition-all hover:border-sky-300 hover:shadow-sm">
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-[#48b3e8] border border-sky-100 group-hover:bg-[#48b3e8] group-hover:text-white transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                </svg>
            </div>
            
            <h3 class="text-sm font-bold text-slate-900 group-hover:text-[#48b3e8] transition">
                <a href="{{ $url }}" class="focus-visible:outline-[#48b3e8]">
                    {{ $name }}
                </a>
            </h3>
        </div>

        @if ($bookCount !== null)
            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600">
                {{ $bookCount }}
            </span>
        @endif
    </div>

    @if ($description)
        <p class="mt-2.5 text-xs text-slate-500 line-clamp-2 leading-relaxed">
            {{ $description }}
        </p>
    @endif
</div>
