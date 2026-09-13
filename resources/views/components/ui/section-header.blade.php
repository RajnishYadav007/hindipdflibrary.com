@props([
    'title',
    'subtitle' => null,
    'badge' => null,
    'actionLabel' => null,
    'actionUrl' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-4 border-b border-slate-200 mb-6']) }}>
    <div>
        <div class="flex items-center gap-2.5">
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
                {{ $title }}
            </h2>
            @if ($badge)
                <x-ui.badge variant="neutral" size="sm">{{ $badge }}</x-ui.badge>
            @endif
        </div>
        @if ($subtitle)
            <p class="mt-1 text-sm text-slate-500 leading-relaxed max-w-2xl">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    @if ($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" class="inline-flex items-center text-sm font-semibold text-brand-700 hover:text-brand-800 transition shrink-0 group">
            <span>{{ $actionLabel }}</span>
            <svg class="ml-1 h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    @elseif (isset($action))
        <div class="shrink-0">
            {{ $action }}
        </div>
    @endif
</div>
