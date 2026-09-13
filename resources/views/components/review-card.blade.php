@props([
    'review' => null,
    'userName' => null,
    'rating' => 5,
    'title' => null,
    'body' => null,
    'date' => null,
    'isVerified' => false,
])

@php
    if ($review) {
        $userName = $userName ?? ($review->user?->name ?? 'Reader');
        $rating = $rating ?? $review->rating;
        $title = $title ?? $review->title;
        $body = $body ?? $review->body;
        $date = $date ?? ($review->created_at ? $review->created_at->format('M d, Y') : null);
        $isVerified = $isVerified ?? (bool) $review->is_verified;
    }

    $initial = mb_substr($userName ?? 'R', 0, 1);
@endphp

<article class="rounded-xl border border-slate-200 bg-white p-4 shadow-2xs">
    <div class="flex items-start justify-between gap-3 mb-2.5">
        <!-- Reviewer Info -->
        <div class="flex items-center gap-2.5">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200">
                {{ $initial }}
            </div>
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-semibold text-slate-900">{{ $userName }}</span>
                    @if ($isVerified)
                        <span class="inline-flex items-center gap-0.5 text-[10px] text-emerald-600 font-medium" title="Verified reader">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                            Verified
                        </span>
                    @endif
                </div>
                @if ($date)
                    <time class="text-[11px] text-slate-400 block">{{ $date }}</time>
                @endif
            </div>
        </div>

        <!-- Rating Display -->
        <x-rating-stars :rating="$rating" :showCount="false" size="xs" />
    </div>

    <!-- Review Content -->
    @if ($title)
        <h4 class="text-xs font-bold text-slate-900 mb-1 leading-snug">
            {{ $title }}
        </h4>
    @endif

    <p class="text-xs text-slate-600 leading-relaxed">
        {{ $body }}
    </p>
</article>
