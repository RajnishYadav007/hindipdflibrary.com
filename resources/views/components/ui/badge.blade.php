@props([
    'variant' => 'neutral', // neutral, primary, success, warning, danger, info
    'size' => 'sm',        // xs, sm, md
])

@php
    $baseClasses = 'inline-flex items-center font-medium rounded-full';

    $sizeClasses = match ($size) {
        'xs' => 'px-2 py-0.5 text-[10px]',
        'md' => 'px-3 py-1 text-sm',
        default => 'px-2.5 py-0.5 text-xs',
    };

    $variantClasses = match ($variant) {
        'primary' => 'bg-brand-50 text-brand-800 border border-brand-200',
        'success' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        'warning' => 'bg-amber-50 text-amber-800 border border-amber-200',
        'danger' => 'bg-red-50 text-red-700 border border-red-200',
        'info' => 'bg-sky-50 text-sky-700 border border-sky-200',
        default => 'bg-slate-100 text-slate-700 border border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "{$baseClasses} {$sizeClasses} {$variantClasses}"]) }}>
    {{ $slot }}
</span>
