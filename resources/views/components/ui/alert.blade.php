@props([
    'variant' => 'info', // info, success, warning, danger
    'title' => null,
])

@php
    $styles = match ($variant) {
        'success' => [
            'wrapper' => 'bg-emerald-50 border-emerald-200 text-emerald-900',
            'icon' => 'text-emerald-600',
            'svg' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'warning' => [
            'wrapper' => 'bg-amber-50 border-amber-200 text-amber-900',
            'icon' => 'text-amber-600',
            'svg' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        ],
        'danger' => [
            'wrapper' => 'bg-red-50 border-red-200 text-red-900',
            'icon' => 'text-red-600',
            'svg' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        default => [
            'wrapper' => 'bg-sky-50 border-sky-200 text-sky-900',
            'icon' => 'text-sky-600',
            'svg' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    };
@endphp

<div role="alert" {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-lg border p-4 text-sm {$styles['wrapper']}"]) }}>
    <svg class="h-5 w-5 shrink-0 mt-0.5 {{ $styles['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $styles['svg'] }}" />
    </svg>
    <div class="flex-1">
        @if ($title)
            <h4 class="font-semibold leading-5 mb-1">{{ $title }}</h4>
        @endif
        <div class="text-sm leading-relaxed opacity-90">
            {{ $slot }}
        </div>
    </div>
</div>
