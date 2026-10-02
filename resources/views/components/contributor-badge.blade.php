@props([
    'user' => null,
    'role' => 'योगदानकर्ता',
    'class' => '',
])

@if($user && ($displayName = $user->public_display_name ?? $user->name))
    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-950 border border-amber-300 text-xs font-medium shadow-2xs {{ $class }}">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-600 text-white text-[11px] font-bold font-serif uppercase shrink-0 shadow-2xs">
            {{ mb_substr(trim($displayName), 0, 1) }}
        </span>
        <span class="truncate max-w-[240px] sm:max-w-xs flex items-center gap-1">
            <span class="text-amber-900 font-semibold">{{ $role }}:</span>
            <strong class="font-bold text-slate-900">{{ $displayName }}</strong>
        </span>
    </div>
@endif
