@props([
    'title' => 'No items found',
    'description' => 'There are no records matching your request at this time.',
    'actionLabel' => null,
    'actionUrl' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-dashed border-slate-300 bg-white p-8 sm:p-12 text-center']) }}>
    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
        {{ $icon ?? null }}
        @if (!isset($icon))
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
        @endif
    </div>
    
    <h3 class="mt-4 text-base font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto leading-relaxed">{{ $description }}</p>

    @if ($actionLabel && $actionUrl)
        <div class="mt-6">
            <x-ui.button :href="$actionUrl" variant="outline" size="sm">
                {{ $actionLabel }}
            </x-ui.button>
        </div>
    @elseif (isset($action))
        <div class="mt-6">
            {{ $action }}
        </div>
    @endif
</div>
