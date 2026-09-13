@props([
    'type' => 'card', // card, text, list
    'count' => 1,
])

@if ($type === 'card')
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6" aria-hidden="true">
        @for ($i = 0; $i < $count; $i++)
            <div class="flex flex-col animate-pulse rounded-lg border border-slate-200 bg-white p-3">
                <div class="w-full aspect-[2/3] bg-slate-200 rounded-md"></div>
                <div class="h-4 bg-slate-200 rounded mt-3 w-4/5"></div>
                <div class="h-3 bg-slate-100 rounded mt-2 w-3/5"></div>
                <div class="h-3 bg-slate-100 rounded mt-2 w-2/5"></div>
            </div>
        @endfor
    </div>
@elseif ($type === 'list')
    <div class="space-y-3 animate-pulse" aria-hidden="true">
        @for ($i = 0; $i < $count; $i++)
            <div class="h-16 bg-slate-100 rounded-lg border border-slate-200 p-4"></div>
        @endfor
    </div>
@else
    <div class="space-y-2 animate-pulse" aria-hidden="true">
        <div class="h-4 bg-slate-200 rounded w-full"></div>
        <div class="h-4 bg-slate-200 rounded w-5/6"></div>
        <div class="h-4 bg-slate-100 rounded w-3/4"></div>
    </div>
@endif
