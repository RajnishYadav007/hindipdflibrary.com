@props([
    'books' => collect(),
    'title' => null,
    'subtitle' => null,
    'actionLabel' => null,
    'actionUrl' => null,
])

<div 
    x-data="{
        container: null,
        canScrollLeft: false,
        canScrollRight: true,
        init() {
            this.container = this.$refs.slider;
            this.updateScrollState();
            this.container.addEventListener('scroll', () => this.updateScrollState(), { passive: true });
        },
        updateScrollState() {
            if (!this.container) return;
            this.canScrollLeft = this.container.scrollLeft > 10;
            this.canScrollRight = this.container.scrollLeft + this.container.clientWidth < this.container.scrollWidth - 10;
        },
        scroll(direction) {
            if (!this.container) return;
            const scrollAmount = this.container.clientWidth * 0.75;
            this.container.scrollBy({
                left: direction === 'next' ? scrollAmount : -scrollAmount,
                behavior: 'smooth'
            });
        }
    }" 
    class="relative group"
>
    <!-- Section Header with Slider Navigation Controls -->
    <div class="mb-5 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            @if ($title)
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-2">
                    <span>{{ $title }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-[#48b3e8] uppercase tracking-wider">
                        Featured
                    </span>
                </h2>
            @endif
            @if ($subtitle)
                <p class="mt-1 text-xs sm:text-sm text-slate-500 max-w-2xl leading-relaxed">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        <div class="flex items-center gap-2.5 self-end sm:self-auto shrink-0">
            @if ($actionUrl && $actionLabel)
                <a href="{{ $actionUrl }}" class="mr-2 text-xs font-semibold text-[#48b3e8] hover:text-[#32a2db] transition flex items-center gap-1 group/link">
                    <span>{{ $actionLabel }}</span>
                    <svg class="h-3.5 w-3.5 transition-transform group-hover/link:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @endif

            <!-- Navigation Buttons -->
            <div class="flex items-center gap-1.5 bg-white p-1 rounded-full border border-slate-200 shadow-xs">
                <button 
                    type="button"
                    @click="scroll('prev')"
                    :disabled="!canScrollLeft"
                    :class="canScrollLeft ? 'text-slate-700 hover:bg-sky-50 hover:text-[#48b3e8]' : 'text-slate-300 cursor-not-allowed'"
                    class="h-7 w-7 rounded-full flex items-center justify-center transition focus-visible:outline-[#48b3e8]"
                    aria-label="Previous books"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button 
                    type="button"
                    @click="scroll('next')"
                    :disabled="!canScrollRight"
                    :class="canScrollRight ? 'text-slate-700 hover:bg-sky-50 hover:text-[#48b3e8]' : 'text-slate-300 cursor-not-allowed'"
                    class="h-7 w-7 rounded-full flex items-center justify-center transition focus-visible:outline-[#48b3e8]"
                    aria-label="Next books"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Horizontal Smooth Sliding Track -->
    <div 
        x-ref="slider"
        class="flex items-stretch gap-4 overflow-x-auto pb-4 pt-1 snap-x snap-mandatory scroll-smooth no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0"
        style="scrollbar-width: none; -ms-overflow-style: none;"
    >
        @forelse ($books as $book)
            <div class="w-[68vw] sm:w-[42vw] md:w-[28vw] lg:w-[15.2rem] shrink-0 snap-start transition duration-200 flex flex-col">
                <x-book-card :book="$book" />
            </div>
        @empty
            <div class="w-full rounded-xl border border-dashed border-slate-200 bg-white p-8 text-center">
                <p class="text-xs text-slate-500">No books available in this collection.</p>
            </div>
        @endforelse
    </div>
</div>
