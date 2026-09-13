@props([
    'variant' => 'primary', // primary, secondary, outline, ghost, danger
    'size' => 'md',        // sm, md, lg
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md transition duration-150 ease-in-out focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-700 disabled:opacity-50 disabled:cursor-not-allowed select-none';

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'lg' => 'px-6 py-3 text-base',
        default => 'px-4 py-2 text-sm',
    };

    $variantClasses = match ($variant) {
        'secondary' => 'bg-slate-100 text-slate-800 hover:bg-slate-200 border border-slate-200',
        'outline' => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 shadow-xs',
        'ghost' => 'text-slate-600 hover:text-slate-900 hover:bg-slate-100',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 shadow-xs focus-visible:outline-red-600',
        default => 'bg-brand-700 text-white hover:bg-brand-800 shadow-xs focus-visible:outline-brand-700',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
