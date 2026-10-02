@props([
    'items' => [], // array of ['label' => '...', 'url' => '...']
])

@php
    // Filter out leading 'Home' or root URL items to prevent duplicate Home breadcrumbs
    $cleanItems = collect($items)->filter(function ($item, $key) {
        if ($key === 0 || $key === '0') {
            $label = strtolower(trim($item['label'] ?? ''));
            $url = trim($item['url'] ?? '');
            if (in_array($label, ['home', 'होम', 'घर', 'मुख्य पृष्ठ'], true)) {
                return false;
            }
            if ($url === route('home') || $url === '/' || $url === url('/') || $url === url('/hi') || $url === url('/en')) {
                return false;
            }
        }
        return true;
    })->values();
@endphp

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'flex text-xs text-slate-500 py-3']) }}>
    <ol class="flex flex-wrap items-center gap-1.5 list-none p-0 m-0">
        {{-- Root Home Link --}}
        <li class="flex items-center">
            <a href="{{ route('home') }}" class="text-slate-500 hover:text-slate-900 transition flex items-center gap-1">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="sr-only sm:not-sr-only">Home</span>
            </a>
        </li>

        @foreach ($cleanItems as $item)
            <li class="flex items-center gap-1.5">
                <svg class="h-3 w-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>

                @if (!empty($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}" class="text-slate-500 hover:text-slate-900 transition">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="font-medium text-slate-900 truncate max-w-xs" aria-current="page">
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
