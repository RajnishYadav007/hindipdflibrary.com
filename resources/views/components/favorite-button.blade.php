@props([
    'isFavorited' => false,
    'bookId' => null,
    'disabled' => false,
    'size' => 'sm', // 'sm', 'md'
])

@php
    $sizeClasses = match ($size) {
        'md' => 'h-9 w-9 text-base',
        default => 'h-7 w-7 text-xs',
    };
    $iconSizeClasses = match ($size) {
        'md' => 'h-5 w-5',
        default => 'h-4 w-4',
    };
    $accessibleLabel = $isFavorited ? 'Remove from favorites' : 'Add to favorites';
@endphp

<button 
    type="button"
    aria-label="{{ $accessibleLabel }}"
    title="{{ $accessibleLabel }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge([
        'class' => "inline-flex items-center justify-center rounded-full transition shadow-xs focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:opacity-40 disabled:cursor-not-allowed {$sizeClasses} " . 
        ($isFavorited 
            ? 'bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100' 
            : 'bg-white/90 backdrop-blur-xs text-slate-500 border border-slate-200 hover:text-rose-600 hover:bg-white')
    ]) }}
>
    <svg 
        class="{{ $iconSizeClasses }} transition-transform hover:scale-110 active:scale-95" 
        fill="{{ $isFavorited ? 'currentColor' : 'none' }}" 
        viewBox="0 0 24 24" 
        stroke="currentColor" 
        stroke-width="{{ $isFavorited ? '1.5' : '2' }}" 
        aria-hidden="true"
    >
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
    </svg>
</button>
