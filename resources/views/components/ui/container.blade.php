@props([
    'as' => 'div',
    'size' => 'default', // 'default' (7xl), 'narrow' (4xl), 'wide' (full)
])

@php
    $sizeClasses = match ($size) {
        'narrow' => 'max-w-4xl',
        'wide' => 'max-w-screen-2xl',
        default => 'max-w-7xl',
    };
@endphp

<{{ $as }} {{ $attributes->merge(['class' => "mx-auto w-full px-4 sm:px-6 lg:px-8 {$sizeClasses}"]) }}>
    {{ $slot }}
</{{ $as }}>
