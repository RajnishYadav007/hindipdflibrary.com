<footer class="border-t border-slate-200 bg-white mt-auto text-slate-600 text-sm">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <!-- Brand & Mission Column -->
            <div class="md:col-span-1 space-y-3">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-700 text-white shadow-xs">
                        <x-application-logo class="h-4 w-4" />
                    </div>
                    <span class="text-base font-bold text-slate-900">
                        {{ config('app.name', 'Hindi PDF Library') }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    A dedicated digital repository providing open, legal access to classic and contemporary Hindi literature, cultural works, and scholarly texts.
                </p>
                <div class="pt-1">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-medium text-emerald-700 border border-emerald-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Free & Cultural Open Access
                    </span>
                </div>
            </div>

            <!-- Library Discovery -->
            <div>
                <h3 class="text-xs font-semibold tracking-wider text-slate-900 uppercase mb-3">
                    Library Catalog
                </h3>
                <ul class="space-y-2 text-xs">
                    <li><a href="#browse-books" class="hover:text-brand-700 transition">All Books (सभी पुस्तकें)</a></li>
                    <li><a href="#browse-authors" class="hover:text-brand-700 transition">Authors Index (लेखक सूची)</a></li>
                    <li><a href="#browse-categories" class="hover:text-brand-700 transition">Subject Categories (श्रेणियाँ)</a></li>
                    <li><a href="#browse-genres" class="hover:text-brand-700 transition">Genres & Forms (विधाएँ)</a></li>
                </ul>
            </div>

            <!-- Library Information -->
            <div>
                <h3 class="text-xs font-semibold tracking-wider text-slate-900 uppercase mb-3">
                    Information & Access
                </h3>
                <ul class="space-y-2 text-xs">
                    <li><a href="#about" class="hover:text-brand-700 transition">About the Library (परिचय)</a></li>
                    <li><a href="#help" class="hover:text-brand-700 transition">Help & FAQs (सहायता)</a></li>
                    <li><a href="#contact" class="hover:text-brand-700 transition">Contact Staff (संपर्क)</a></li>
                    <li><a href="#contribute" class="hover:text-brand-700 transition">Suggest a Book (सुझाव)</a></li>
                </ul>
            </div>

            <!-- Legal & Rights -->
            <div>
                <h3 class="text-xs font-semibold tracking-wider text-slate-900 uppercase mb-3">
                    Rights & Policies
                </h3>
                <ul class="space-y-2 text-xs">
                    <li><a href="#privacy" class="hover:text-brand-700 transition">Privacy Policy (गोपनीयता)</a></li>
                    <li><a href="#terms" class="hover:text-brand-700 transition">Terms of Service (सेवा शर्तें)</a></li>
                    <li><a href="#copyright" class="hover:text-brand-700 transition">Copyright & DMCA (कॉपीराइट नीति)</a></li>
                    <li><a href="#public-domain" class="hover:text-brand-700 transition">Public Domain Statement</a></li>
                </ul>
            </div>
        </div>

        <!-- Sub-footer Bottom Bar -->
        <div class="border-t border-slate-100 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Hindi PDF Library') }}. All rights reserved.</p>
            <p class="text-slate-400">
                Non-commercial educational and cultural literature preservation initiative.
            </p>
        </div>
    </div>
</footer>
