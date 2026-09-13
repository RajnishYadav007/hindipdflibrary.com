@props([
    'action' => route('search'),
    'method' => 'GET',
    'placeholder' => 'Search books, authors, ISBN, topics...',
    'value' => null,
    'size' => 'default', // 'default', 'large'
    'autofocus' => false,
])

@php
    $inputClasses = match ($size) {
        'large' => 'py-3.5 pl-12 pr-24 text-base',
        default => 'py-2 pl-10 pr-16 text-sm',
    };
    $iconClasses = match ($size) {
        'large' => 'left-4 h-5 w-5',
        default => 'left-3 h-4 w-4',
    };
@endphp

<form action="{{ $action }}" method="{{ $method }}" role="search" {{ $attributes->merge(['class' => 'relative w-full']) }}>
    <label for="site-search-input" class="sr-only">Search Library</label>
    
    <div class="relative flex items-center w-full">
        <!-- Search Icon -->
        <div class="pointer-events-none absolute inset-y-0 {{ $iconClasses }} flex items-center text-slate-400" aria-hidden="true">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <input 
            type="search" 
            name="q" 
            id="site-search-input"
            value="{{ $value }}" 
            placeholder="{{ $placeholder }}"
            @if ($autofocus) autofocus @endif
            class="block w-full rounded-lg border border-slate-300 bg-white placeholder-slate-400 text-slate-900 transition focus:border-brand-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-600/20 {{ $inputClasses }}"
            autocomplete="off"
        />

        <!-- Submit Button -->
        <div class="absolute right-1.5 inset-y-1.5 flex items-center">
            <button 
                type="submit" 
                aria-label="Submit search"
                class="inline-flex items-center justify-center rounded-md bg-brand-700 px-2.5 py-1 text-xs font-semibold text-white transition hover:bg-brand-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 shadow-xs"
            >
                Search
            </button>
        </div>
    </div>
</form>
