@props([
    'rating' => 0,          // Float or int rating (0.00 to 5.00)
    'ratingCount' => null,  // Total count of ratings
    'size' => 'sm',         // 'xs', 'sm', 'md'
    'showCount' => true,
])

@php
    $rating = (float) $rating;
    $hasRatings = $rating > 0 || ($ratingCount !== null && $ratingCount > 0);
    $starSizeClasses = match ($size) {
        'xs' => 'h-3 w-3',
        'md' => 'h-5 w-5',
        default => 'h-4 w-4',
    };
    $textSizeClasses = match ($size) {
        'xs' => 'text-[11px]',
        'md' => 'text-sm',
        default => 'text-xs',
    };
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5']) }}
     @if ($hasRatings)
         aria-label="Rated {{ number_format($rating, 1) }} out of 5 stars @if($ratingCount) ({{ $ratingCount }} ratings) @endif"
     @else
         aria-label="No ratings yet"
     @endif
>
    @if ($hasRatings)
        <div class="flex items-center text-amber-500" aria-hidden="true">
            @for ($i = 1; $i <= 5; $i++)
                @if ($rating >= $i)
                    {{-- Full Star --}}
                    <svg class="{{ $starSizeClasses }} fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @elseif ($rating > ($i - 1) && $rating < $i)
                    {{-- Half Star --}}
                    <svg class="{{ $starSizeClasses }}" viewBox="0 0 20 20">
                        <defs>
                            <linearGradient id="half-star-grad-{{ $size }}-{{ $i }}">
                                <stop offset="50%" stop-color="#f59e0b" />
                                <stop offset="50%" stop-color="#e2e8f0" />
                            </linearGradient>
                        </defs>
                        <path fill="url(#half-star-grad-{{ $size }}-{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @else
                    {{-- Empty Star --}}
                    <svg class="{{ $starSizeClasses }} text-slate-200 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @endif
            @endfor
        </div>

        <span class="font-semibold text-slate-800 {{ $textSizeClasses }}">
            {{ number_format($rating, 1) }}
        </span>

        @if ($showCount && $ratingCount !== null)
            <span class="text-slate-400 {{ $textSizeClasses }}">
                ({{ number_format($ratingCount) }})
            </span>
        @endif
    @else
        <span class="text-slate-400 italic {{ $textSizeClasses }}">
            No ratings yet
        </span>
    @endif
</div>
