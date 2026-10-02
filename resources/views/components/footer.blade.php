<footer class="border-t border-slate-200 bg-white text-slate-600 text-sm mt-auto">
    <div class="mx-auto max-w-7xl px-4 pt-14 pb-10 sm:px-6 lg:px-8">
        <!-- Main Footer Grid (5 Columns) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 mb-12">
            <!-- Brand & Mission Column (Span 4) -->
            <div class="lg:col-span-4 space-y-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-amber-600 to-orange-500 text-white shadow-sm ring-1 ring-orange-500/20">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </span>
                    <div>
                        <span class="block text-base font-bold text-slate-900 tracking-tight leading-tight">
                            हिंदी पीडीएफ लाइब्रेरी
                        </span>
                        <span class="block text-[11px] font-medium text-slate-500">
                            {{ config('app.name', 'Hindi PDF Library') }} • 100% Free Knowledge
                        </span>
                    </div>
                </a>

                <p class="text-xs text-slate-500 leading-relaxed max-w-sm">
                    भारत का सम्पूर्ण व निःशुल्क डिजिटल पुस्तकालय। कालजयी हिंदी साहित्य, पावन चालीसा, बाल कहानियाँ व ऑडियो पुस्तकों का संरक्षित सांस्कृतिक संग्रह।
                </p>

                <div class="flex items-center gap-2 pt-1">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 border border-emerald-200/60">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        100% मुफ़्त और ओपन एक्सेस
                    </span>
                </div>

                <!-- Social Icons -->
                <div class="pt-2">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2.5">हमसे जुड़ें</p>
                    <div class="flex items-center gap-2">
                        <a href="https://t.me" target="_blank" rel="noopener noreferrer" class="h-8 w-8 rounded-lg bg-slate-100 hover:bg-orange-50 hover:text-orange-600 text-slate-600 flex items-center justify-center transition" aria-label="Telegram">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .37z"/>
                            </svg>
                        </a>
                        <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" class="h-8 w-8 rounded-lg bg-slate-100 hover:bg-orange-50 hover:text-orange-600 text-slate-600 flex items-center justify-center transition" aria-label="YouTube">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="h-8 w-8 rounded-lg bg-slate-100 hover:bg-orange-50 hover:text-orange-600 text-slate-600 flex items-center justify-center transition" aria-label="Facebook">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="h-8 w-8 rounded-lg bg-slate-100 hover:bg-orange-50 hover:text-orange-600 text-slate-600 flex items-center justify-center transition" aria-label="X / Twitter">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Column 2: कालजयी साहित्य व उपन्यास (Span 2) -->
            <div class="lg:col-span-2">
                <h3 class="text-xs font-bold tracking-wider text-slate-900 uppercase mb-3 flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-orange-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>कालजयी साहित्य व उपन्यास</span>
                </h3>
                <ul class="space-y-2.5 text-xs text-slate-600">
                    <li><a href="{{ route('books.show', 'godaan') }}" class="hover:text-orange-600 transition">गोदान (प्रेमचंद)</a></li>
                    <li><a href="{{ route('books.show', 'bhagavad-gita') }}" class="hover:text-orange-600 transition">श्रीमद्भगवद्गीता</a></li>
                    <li><a href="{{ route('books.show', 'ramcharitmanas') }}" class="hover:text-orange-600 transition">श्रीरामचरितमानस</a></li>
                    <li><a href="{{ route('books.index') }}" class="hover:text-orange-600 transition">सभी पुस्तकें (All Books)</a></li>
                </ul>
            </div>

            <!-- Column 3: पावन चालीसा व स्तोत्र (Span 2) -->
            <div class="lg:col-span-2">
                <h3 class="text-xs font-bold tracking-wider text-slate-900 uppercase mb-3 flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-orange-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                    </svg>
                    <span>पावन चालीसा व स्तोत्र</span>
                </h3>
                <ul class="space-y-2.5 text-xs text-slate-600">
                    <li><a href="{{ route('books.show', 'hanuman-chalisa') }}" class="hover:text-orange-600 transition">श्री हनुमान चालीसा</a></li>
                    <li><a href="{{ route('books.show', 'hanuman-aarti') }}" class="hover:text-orange-600 transition">श्री हनुमान आरती</a></li>
                    <li><a href="{{ route('books.show', 'bajrang-baan') }}" class="hover:text-orange-600 transition">श्री बजरंग बाण</a></li>
                    <li><a href="{{ route('ved-puran') }}" class="hover:text-orange-600 transition">चारों वेद एवं १८ महापुराण</a></li>
                </ul>
            </div>

            <!-- Column 4: बाल साहित्य व कहानियाँ (Span 2) -->
            <div class="lg:col-span-2">
                <h3 class="text-xs font-bold tracking-wider text-slate-900 uppercase mb-3 flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                    <span>बाल साहित्य व कहानियाँ</span>
                </h3>
                <ul class="space-y-2.5 text-xs text-slate-600">
                    <li><a href="{{ route('books.show', 'panchatantra-ki-kahaniyan') }}" class="hover:text-orange-600 transition">पंचतंत्र की कहानियाँ</a></li>
                    <li><a href="{{ route('books.show', 'akbar-birbal-ki-kahaniyan') }}" class="hover:text-orange-600 transition">अकबर और बीरबल</a></li>
                    <li><a href="{{ route('books.show', 'tenalirama-ki-kahaniyan') }}" class="hover:text-orange-600 transition">तेनालीराम की चतुराई</a></li>
                    <li><a href="{{ route('categories.show', 'children-stories') }}" class="hover:text-orange-600 transition">सम्पूर्ण बाल संग्रह</a></li>
                </ul>
            </div>

            <!-- Column 5: परिचय एवं नीतियाँ (Span 2) -->
            <div class="lg:col-span-2">
                <h3 class="text-xs font-bold tracking-wider text-slate-900 uppercase mb-3 flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-orange-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span>परिचय एवं नीतियाँ</span>
                </h3>
                <ul class="space-y-2.5 text-xs text-slate-600">
                    <li><a href="{{ route('pages.about') }}" class="hover:text-orange-600 transition">हमारे बारे में (About)</a></li>
                    <li><a href="{{ route('pages.help') }}" class="hover:text-orange-600 transition">सहायता (FAQs)</a></li>
                    <li><a href="{{ route('pages.contact') }}" class="hover:text-orange-600 transition">संपर्क सूत्र (Contact)</a></li>
                    <li><a href="{{ route('pages.copyright') }}" class="hover:text-orange-600 transition">कॉपीराइट (DMCA)</a></li>
                    <li><a href="{{ route('pages.privacy') }}" class="hover:text-orange-600 transition">गोपनीयता नीति</a></li>
                </ul>
            </div>
        </div>

        <!-- Sub-footer Bottom Bar -->
        <div class="border-t border-slate-100 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Hindi PDF Library') }}. सर्वाधिकार सुरक्षित।</p>
            <p class="flex items-center gap-1 text-slate-400">
                <span>हिंदी साहित्य व संस्कृति के प्रसार हेतु समर्पित</span>
                <span class="text-rose-500">♥</span>
            </p>
            <div class="flex items-center gap-3 text-[11px] text-slate-400">
                <span class="inline-flex items-center gap-1">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    सुरक्षित सर्वर
                </span>
                <span>•</span>
                <span>हिंदी भाषा संस्करण</span>
            </div>
        </div>
    </div>
</footer>