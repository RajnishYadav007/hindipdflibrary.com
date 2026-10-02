@props([
    'action' => route('search'),
    'method' => 'GET',
    'placeholder' => 'Search books, authors, ISBN, topics...',
    'value' => null,
    'size' => 'default', // 'default', 'large'
    'autofocus' => false,
    'id' => null,
])

@php
    $inputId = $id ?? 'site-search-' . substr(md5($action . $placeholder . $size), 0, 8);
    $inputClasses = match ($size) {
        'large' => 'py-3.5 sm:py-4 pl-12 sm:pl-14 pr-28 sm:pr-32 text-sm sm:text-base rounded-full shadow-xs border-slate-200 focus:border-sky-400 focus:ring-sky-400/20',
        default => 'py-2.5 pl-10 pr-24 text-sm rounded-full border-slate-200 focus:border-sky-400 focus:ring-sky-400/20',
    };
    $iconWrapperClasses = match ($size) {
        'large' => 'left-4 sm:left-5',
        default => 'left-3.5',
    };
    $iconSvgClasses = match ($size) {
        'large' => 'h-5 w-5',
        default => 'h-4 w-4',
    };
    $buttonClasses = match ($size) {
        'large' => 'px-5 py-2 text-xs sm:text-sm font-bold rounded-full bg-[#48b3e8] hover:bg-[#32a2db] text-white shadow-xs',
        default => 'px-3.5 py-1.5 text-xs font-bold rounded-full bg-[#48b3e8] hover:bg-[#32a2db] text-white shadow-xs',
    };
@endphp

<div 
    x-data="{
        query: '{{ addslashes($value ?? '') }}',
        isOpen: false,
        loading: false,
        results: { books: [], authors: [], categories: [] },
        selectedIndex: -1,
        allItems: [],

        init() {
            // Global keyboard shortcut to focus search: '/' or 'Ctrl+K' / 'Cmd+K'
            window.addEventListener('keydown', (e) => {
                const target = e.target;
                const isInput = target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.isContentEditable;
                if (!isInput && (e.key === '/' || ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k'))) {
                    e.preventDefault();
                    document.getElementById('{{ $inputId }}')?.focus();
                }
            });
        },

        fetchSuggestions() {
            if (this.query.trim().length < 2) {
                this.results = { books: [], authors: [], categories: [] };
                this.allItems = [];
                this.isOpen = false;
                return;
            }

            this.loading = true;
            fetch('{{ route('search.suggest') }}?q=' + encodeURIComponent(this.query.trim()))
                .then(res => res.json())
                .then(data => {
                    this.results = data;
                    this.buildItemsList();
                    this.isOpen = (this.results.books.length > 0 || this.results.authors.length > 0 || this.results.categories.length > 0);
                    this.selectedIndex = -1;
                })
                .catch(() => {
                    this.results = { books: [], authors: [], categories: [] };
                    this.allItems = [];
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        buildItemsList() {
            this.allItems = [];
            this.results.books.forEach(b => this.allItems.push({ type: 'book', ...b }));
            this.results.authors.forEach(a => this.allItems.push({ type: 'author', ...a }));
            this.results.categories.forEach(c => this.allItems.push({ type: 'category', ...c }));
        },

        onKeyDown(e) {
            if (!this.isOpen || this.allItems.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.selectedIndex = (this.selectedIndex + 1) % this.allItems.length;
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.selectedIndex = (this.selectedIndex - 1 + this.allItems.length) % this.allItems.length;
            } else if (e.key === 'Enter') {
                if (this.selectedIndex >= 0 && this.allItems[this.selectedIndex]) {
                    e.preventDefault();
                    window.location.href = this.allItems[this.selectedIndex].url;
                }
            } else if (e.key === 'Escape') {
                this.isOpen = false;
                this.selectedIndex = -1;
            }
        },

        close() {
            this.isOpen = false;
            this.selectedIndex = -1;
        }
    }"
    @click.outside="close()"
    class="relative w-full"
>
    <form action="{{ $action }}" method="{{ $method }}" role="search" class="relative w-full">
        <label for="{{ $inputId }}" class="sr-only">Search Library</label>
        
        <div class="relative flex items-center w-full">
            <!-- Search Icon -->
            <div class="pointer-events-none absolute {{ $iconWrapperClasses }} top-1/2 -translate-y-1/2 flex items-center justify-center text-slate-400" aria-hidden="true">
                <template x-if="!loading">
                    <svg class="{{ $iconSvgClasses }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </template>
                <template x-if="loading">
                    <svg class="{{ $iconSvgClasses }} shrink-0 animate-spin text-terracotta-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </template>
            </div>

            <input 
                type="search" 
                name="q" 
                id="{{ $inputId }}"
                x-model="query"
                @input.debounce.250ms="fetchSuggestions()"
                @keydown="onKeyDown($event)"
                @focus="if (query.trim().length >= 2 && allItems.length > 0) isOpen = true"
                placeholder="{{ $placeholder }}"
                @if ($autofocus) autofocus @endif
                class="block w-full border bg-white placeholder-slate-400 text-ink-900 transition focus:bg-white focus:outline-none focus:ring-2 {{ $inputClasses }}"
                autocomplete="off"
                aria-autocomplete="list"
                :aria-expanded="isOpen"
            />

            <!-- Shortcut Hint / Submit Button -->
            <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                <kbd class="hidden sm:inline-block px-1.5 py-0.5 text-[10px] font-mono text-slate-400 bg-slate-100 border border-slate-200 rounded">
                    /
                </kbd>
                <button 
                    type="submit" 
                    aria-label="Submit search"
                    class="inline-flex items-center justify-center bg-ink-900 hover:bg-ink-800 text-white transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ink-800 shadow-xs active:scale-95 {{ $buttonClasses }}"
                >
                    Search
                </button>
            </div>
        </div>
    </form>

    <!-- Live Autocomplete Suggestion Panel -->
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        class="absolute left-0 right-0 z-50 mt-2 max-h-96 overflow-y-auto rounded-2xl border border-warm-300 bg-white/98 backdrop-blur-md p-3 shadow-xl text-left"
        style="display: none;"
    >
        <!-- Books Section -->
        <template x-if="results.books && results.books.length > 0">
            <div class="mb-3">
                <div class="px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Books (पुस्तकें)
                </div>
                <div class="space-y-1 mt-1">
                    <template x-for="(book, idx) in results.books" :key="'book-' + book.id">
                        <a 
                            :href="book.url"
                            :class="selectedIndex === idx ? 'bg-warm-100 text-ink-900' : 'hover:bg-warm-50 text-slate-800'"
                            class="flex items-center gap-3 px-2.5 py-2 rounded-xl transition group"
                        >
                            <div class="w-8 h-11 shrink-0 rounded bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center">
                                <template x-if="book.cover_url">
                                    <img :src="book.cover_url" :alt="book.title" class="w-full h-full object-cover" />
                                </template>
                                <template x-if="!book.cover_url">
                                    <span class="text-xs">📖</span>
                                </template>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs sm:text-sm font-bold truncate group-hover:text-terracotta-600 transition" x-text="book.title"></h4>
                                <p class="text-[11px] text-slate-500 truncate" x-text="book.author"></p>
                            </div>
                            <svg class="h-4 w-4 text-slate-300 group-hover:text-slate-500 transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </template>
                </div>
            </div>
        </template>

        <!-- Authors Section -->
        <template x-if="results.authors && results.authors.length > 0">
            <div class="mb-3 border-t border-warm-200 pt-2">
                <div class="px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Authors (लेखक)
                </div>
                <div class="space-y-1 mt-1">
                    <template x-for="(author, aIdx) in results.authors" :key="'author-' + author.id">
                        <a 
                            :href="author.url"
                            :class="selectedIndex === (results.books.length + aIdx) ? 'bg-warm-100 text-ink-900' : 'hover:bg-warm-50 text-slate-800'"
                            class="flex items-center justify-between px-2.5 py-1.5 rounded-xl transition group"
                        >
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="h-6 w-6 rounded-full bg-sage-50 text-sage-700 flex items-center justify-center text-xs font-bold shrink-0">
                                    ✍️
                                </span>
                                <span class="text-xs font-bold truncate group-hover:text-terracotta-600 transition" x-text="author.name"></span>
                            </div>
                            <span class="text-[11px] text-slate-400 shrink-0" x-text="author.books_count + ' titles'"></span>
                        </a>
                    </template>
                </div>
            </div>
        </template>

        <!-- Categories Section -->
        <template x-if="results.categories && results.categories.length > 0">
            <div class="mb-2 border-t border-warm-200 pt-2">
                <div class="px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                    Categories (श्रेणियाँ)
                </div>
                <div class="flex flex-wrap gap-1.5 px-2 mt-1">
                    <template x-for="(cat, cIdx) in results.categories" :key="'cat-' + cat.id">
                        <a 
                            :href="cat.url"
                            class="inline-flex items-center gap-1 rounded-lg bg-warm-100 hover:bg-warm-200 px-2.5 py-1 text-xs font-semibold text-slate-700 transition"
                        >
                            <span x-text="cat.name"></span>
                            <span class="text-[10px] text-slate-400" x-text="'(' + cat.books_count + ')'"></span>
                        </a>
                    </template>
                </div>
            </div>
        </template>

        <!-- Footer Callout -->
        <div class="border-t border-warm-200 pt-2.5 px-2 flex items-center justify-between text-[11px] text-slate-500">
            <span>Press <kbd class="px-1 py-0.5 font-mono bg-slate-100 rounded text-[10px]">Enter</kbd> to search all</span>
            <a :href="'{{ $action }}?q=' + encodeURIComponent(query)" class="font-bold text-terracotta-600 hover:underline">
                View all results &rarr;
            </a>
        </div>
    </div>
</div>
