@props([
    'icon' => 'file-text',
    'title' => 'कोई सामग्री उपलब्ध नहीं है',
    'description' => null,
    'actionUrl' => null,
    'actionLabel' => null,
    'actionIcon' => 'plus',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-dashed border-slate-300 bg-white p-8 sm:p-12 text-center']) }}>
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 mb-3">
        <x-app-icon :name="$icon" class="h-6 w-6" />
    </div>
    <h3 class="text-sm font-bold text-slate-900 font-serif">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
            {{ $description }}
        </p>
    @endif
    @if ($actionUrl && $actionLabel)
        <div class="mt-4">
            <a 
                href="{{ $actionUrl }}" 
                class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 shadow-xs transition"
            >
                @if ($actionIcon)
                    <x-app-icon :name="$actionIcon" class="h-3.5 w-3.5" />
                @endif
                <span>{{ $actionLabel }}</span>
            </a>
        </div>
    @endif
    {{ $slot }}
</div>