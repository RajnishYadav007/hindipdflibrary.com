@props([
    'paginator' => null,
])

@if ($paginator && $paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" {{ $attributes->merge(['class' => 'flex items-center justify-between border-t border-slate-200 px-4 py-3 sm:px-6 mt-8']) }}>
        <!-- Mobile View (Previous / Next Buttons) -->
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-xs font-medium text-slate-400 cursor-not-allowed">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                    Previous
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative ml-3 inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                    Next
                </a>
            @else
                <span class="relative ml-3 inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-xs font-medium text-slate-400 cursor-not-allowed">
                    Next
                </span>
            @endif
        </div>

        <!-- Desktop View (Page Counter & Number Links) -->
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-slate-600">
                    Showing
                    <span class="font-semibold text-slate-900">{{ $paginator->firstItem() }}</span>
                    to
                    <span class="font-semibold text-slate-900">{{ $paginator->lastItem() }}</span>
                    of
                    <span class="font-semibold text-slate-900">{{ $paginator->total() }}</span>
                    results
                </p>
            </div>

            <div>
                <span class="isolate inline-flex -space-x-px rounded-md shadow-2xs">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Previous Page" class="relative inline-flex items-center rounded-l-md px-2 py-1.5 text-slate-300 ring-1 ring-inset ring-slate-300 cursor-not-allowed">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous Page" class="relative inline-flex items-center rounded-l-md px-2 py-1.5 text-slate-500 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition focus:z-20">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                        </a>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next Page" class="relative inline-flex items-center rounded-r-md px-2 py-1.5 text-slate-500 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition focus:z-20">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Next Page" class="relative inline-flex items-center rounded-r-md px-2 py-1.5 text-slate-300 ring-1 ring-inset ring-slate-300 cursor-not-allowed">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
