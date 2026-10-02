<div 
    x-data="{
        isOpen: false,
        book: {},
        openDrawer(bookData) {
            this.book = bookData;
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },
        closeDrawer() {
            this.isOpen = false;
            document.body.style.overflow = '';
        }
    }"
    x-init="
        window.addEventListener('open-book-drawer', (e) => {
            openDrawer(e.detail);
        });
    "
    x-show="isOpen"
    x-cloak
    class="fixed inset-0 z-50 overflow-hidden"
    style="display: none;"
>
    <!-- Backdrop -->
    <div 
        x-show="isOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="closeDrawer()"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
    ></div>

    <!-- Drawer Panel (Image 1 Style: Dark Navy Theme) -->
    <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
        <div 
            x-show="isOpen"
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="w-screen max-w-md bg-[#111625] text-white p-6 sm:p-8 flex flex-col justify-between overflow-y-auto shadow-2xl border-l border-slate-800"
        >
            <div>
                <!-- Top Bar -->
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-4 mb-6">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300">
                        About the book (पुस्तक परिचय)
                    </h3>
                    <button 
                        type="button" 
                        @click="closeDrawer()"
                        class="rounded-lg p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 transition"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Book Cover Preview -->
                <div class="flex justify-center mb-6">
                    <div class="w-44 aspect-[2/3] overflow-hidden rounded-2xl border-2 border-white/10 shadow-2xl bg-slate-800">
                        <template x-if="book.coverUrl">
                            <img :src="book.coverUrl" :alt="book.title" class="h-full w-full object-cover" />
                        </template>
                        <template x-if="!book.coverUrl">
                            <div class="h-full w-full flex flex-col items-center justify-center p-3 text-center bg-gradient-to-b from-brand-900 to-slate-900">
                                <span class="text-3xl">📖</span>
                                <span class="text-xs font-bold mt-2 text-white line-clamp-2" x-text="book.title"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Book Title & Author -->
                <div class="text-center space-y-1 mb-5">
                    <h2 class="text-xl font-extrabold text-white leading-tight" x-text="book.title"></h2>
                    <p class="text-xs font-semibold text-[#48b3e8]" x-text="book.author"></p>
                </div>

                <!-- Stats Pill Row (Image 1 Style) -->
                <div class="rounded-2xl bg-slate-800/80 border border-slate-700/60 p-3.5 mb-6 flex items-center justify-around text-center text-xs">
                    <div>
                        <span class="block font-bold text-white text-sm" x-text="book.pages || '250+'"></span>
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider">Pages</span>
                    </div>
                    <div class="h-6 w-px bg-slate-700"></div>
                    <div>
                        <span class="block font-bold text-white text-sm" x-text="book.reviewsCount || '10+'"></span>
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider">Reviews</span>
                    </div>
                    <div class="h-6 w-px bg-slate-700"></div>
                    <div>
                        <span class="block font-bold text-amber-400 text-sm" x-text="(book.rating || '4.5') + ' ★'"></span>
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider">Rating</span>
                    </div>
                </div>

                <!-- Plot / Synopsis Section (Image 1 Style) -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Plot &amp; Summary (कथासार)
                    </h4>
                    <p class="text-xs text-slate-300 leading-relaxed max-h-40 overflow-y-auto pr-1" x-text="book.description || 'इस पुस्तक का संक्षिप्त विवरण उपलब्ध है। हिन्दी डिजिटल पुस्तकालय में इसका पूरा पाठ निःशुल्क उपलब्ध है।'"></p>
                </div>
            </div>

            <!-- Bottom Action Buttons -->
            <div class="pt-6 border-t border-slate-800/80 space-y-2.5 mt-6">
                <!-- Large Read Pill Button (Booknest Cyan Style) -->
                <a 
                    :href="book.url || '#'" 
                    class="w-full flex items-center justify-center rounded-full bg-[#48b3e8] px-5 py-3 text-xs font-bold text-white hover:bg-[#32a2db] shadow-lg shadow-sky-500/20 transition hover:scale-102"
                >
                    Read Book (पुस्तक पढ़ें)
                </a>

                <div class="grid grid-cols-2 gap-2">
                    <!-- Interactive Flip Reader -->
                    <a 
                        :href="(book.url ? book.url + '/reader' : '#')" 
                        class="flex items-center justify-center gap-1.5 rounded-full bg-slate-800 border border-slate-700 px-4 py-2.5 text-xs font-bold text-sky-400 hover:text-white hover:bg-slate-700 shadow-xs transition hover:scale-102"
                    >
                        <svg class="h-3.5 w-3.5 fill-current text-sky-400" viewBox="0 0 24 24">
                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                        </svg>
                        <span>3D Flipbook (फ्लिपबुक)</span>
                    </a>

                    <!-- Download Button -->
                    <a 
                        :href="book.downloadUrl || '#'" 
                        class="flex items-center justify-center gap-1.5 rounded-full border border-slate-700 bg-slate-800/90 px-3 py-2 text-xs font-semibold text-emerald-400 hover:bg-slate-700 transition"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>Download PDF</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
