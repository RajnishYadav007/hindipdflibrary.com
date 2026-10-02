@props([
    'books' => collect(),
    'title' => 'Listen to Books',
])

<div class="rounded-[24px] border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2.5">
            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-sky-50 text-[#48b3e8]">
                <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                    <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">
                    {{ $title }}
                </h3>
                <span class="text-[11px] text-slate-500 font-medium">ऑडियो पुस्तकें सुनें</span>
            </div>
        </div>
        <a href="{{ route('books.index') }}" class="text-xs font-semibold text-[#48b3e8] hover:text-[#32a2db] hover:underline">
            View all
        </a>
    </div>

    <div class="divide-y divide-slate-100">
        @forelse ($books as $book)
            @php
                $bookTitle = $book->title;
                $bookAuthor = $book->authors->first()?->name ?? 'Hindi Author';
                $bookSynopsis = $book->description ?: ($bookTitle . ' by ' . $bookAuthor . '. Discover and read classic Hindi literature on Hindi PDF Library.');
                $coverUrl = $book->cover_url;
            @endphp
            <div class="py-3 flex items-center justify-between gap-3 group">
                <div class="flex items-center gap-3 min-w-0">
                    <!-- Mini Cover -->
                    <div class="relative h-13 w-9 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50 shadow-xs">
                        @if ($coverUrl)
                            <img src="{{ $coverUrl }}" alt="{{ $bookTitle }}" class="h-full w-full object-cover group-hover:scale-105 transition duration-200" loading="lazy" />
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gradient-to-br from-[#2c2018] to-[#453326] text-amber-200 font-serif text-[10px] font-bold text-center px-0.5">
                                {{ mb_substr($bookTitle, 0, 2) }}
                            </div>
                        @endif
                    </div>

                    <!-- Book Info -->
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#48b3e8] transition truncate">
                            <a href="{{ route('books.show', $book->slug) }}">
                                {{ $bookTitle }}
                            </a>
                        </h4>
                        <p class="text-[11px] text-slate-500 truncate mt-0.5">
                            {{ $bookAuthor }}
                        </p>
                    </div>
                </div>

                <!-- Circular Sky-Blue Play Button (from Booknest design) -->
                <button 
                    type="button" 
                    @click="window.dispatchEvent(new CustomEvent('play-book-audio', { detail: { title: '{{ addslashes($bookTitle) }}', author: '{{ addslashes($bookAuthor) }}', text: '{{ addslashes(preg_replace('/\s+/', ' ', $bookSynopsis)) }}' } }))"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#48b3e8] hover:bg-[#32a2db] text-white shadow-xs transition hover:scale-105 active:scale-95"
                    title="Listen to {{ $bookTitle }}"
                    aria-label="Listen to {{ $bookTitle }}"
                >
                    <svg class="h-4 w-4 fill-current ml-0.5" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" />
                    </svg>
                </button>
            </div>
        @empty
            <div class="py-6 text-center text-xs text-slate-400">
                ऑडियो पुस्तकें शीघ्र उपलब्ध होंगी।
            </div>
        @endforelse
    </div>
</div>
