<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-slate-700">डैशबोर्ड</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('dashboard', ['tab' => 'submissions']) }}" class="hover:text-slate-700">मेरी प्रस्तुतियां</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-700 font-semibold">ऑडियोबुक सबमिट करें</span>
                </nav>
                <h1 class="font-extrabold text-2xl text-slate-900 leading-tight font-serif">
                    नई ऑडियोबुक सबमिट करें (Submit Audiobook)
                </h1>
            </div>
            <a href="{{ route('dashboard', ['tab' => 'submissions']) }}" class="px-3.5 py-1.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                &larr; वापस जाएं
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-amber-50/25 min-h-[calc(100vh-12rem)]" 
         x-data="{
             showPreview: false,
             title: '{{ old('title', '') }}',
             title_en: '{{ old('title_en', '') }}',
             author_name: '{{ old('author_name', '') }}',
             narrator: '{{ old('narrator', '') }}',
             description: '{{ old('description', '') }}',
             languageName: '',
             addChaptersNext: false,
             sourceMode: 'upload',
             init() {
                 let sel = document.querySelector('select[name=language_id]');
                 if (sel && sel.selectedOptions[0]) {
                     this.languageName = sel.selectedOptions[0].text;
                 }
             }
         }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
                
                <!-- Guidelines -->
                <div class="mb-6 p-4 rounded-xl bg-amber-50/80 border border-amber-200 text-xs text-amber-900 flex items-start gap-3">
                    <div class="p-1 rounded-lg bg-amber-100 text-amber-800 shrink-0 mt-0.5">
                        <x-app-icon name="headphones" class="h-4 w-4" />
                    </div>
                    <div>
                        <strong class="block mb-1">ऑडियोबुक सबमिशन दिशा-निर्देश:</strong>
                        <ul class="list-disc list-inside space-y-1 text-amber-800">
                            <li>केवल वैध कॉपीराइट अनुमति या पब्लिक डोमेन ऑडियो ही स्वीकार्य हैं।</li>
                            <li>स्वीकृत ऑडियो प्रारूप: <strong>MP3, M4A, AAC</strong> (अधिकतम 50MB प्रति फ़ाइल)।</li>
                            <li>यदि ऑडियोबुक में कई अध्याय/ट्रैक हैं, तो आप पहले विवरण भरकर अगले चरण में अध्याय जोड़ सकते हैं।</li>
                            <li>सबमिशन के बाद यह <code>Pending Review</code> स्थिति में रहेगा और एडमिन सत्यापन के बाद लाइव होगा।</li>
                        </ul>
                    </div>
                </div>

                <form id="audiobookForm" action="{{ route('dashboard.submissions.audiobook.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            ऑडियोबुक शीर्षक (Title in Hindi) *
                        </label>
                        <input type="text" name="title" x-model="title" value="{{ old('title') }}" required placeholder="उदा. गोदान (ऑडियो संस्करण)" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500 focus:ring-amber-500">
                        @error('title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                शीर्षक अंग्रेज़ी में (Title in English)
                            </label>
                            <input type="text" name="title_en" x-model="title_en" value="{{ old('title_en') }}" placeholder="e.g. Godan (Audiobook Edition)" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                मूल लेखक (Author / Original Writer)
                            </label>
                            <input type="text" name="author_name" x-model="author_name" value="{{ old('author_name') }}" placeholder="उदा. मुंशी प्रेमचंद" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500 focus:ring-amber-500">
                            @error('author_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                भाषा (Language) *
                            </label>
                            <select name="language_id" required @change="languageName = $event.target.selectedOptions[0].text" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500">
                                @foreach($languages as $lang)
                                    <option value="{{ $lang->id }}" {{ old('language_id') == $lang->id ? 'selected' : '' }}>{{ $lang->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                वाचक / स्वर (Narrator)
                            </label>
                            <input type="text" name="narrator" x-model="narrator" value="{{ old('narrator') }}" placeholder="उदा. समीर गोस्वामी / वाचक का नाम" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500">
                        </div>
                    </div>

                    <!-- Optional Book Link -->
                    @if(isset($books) && $books->isNotEmpty())
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                पुस्तकालय में उपलब्ध ई-बुक से जोड़ें (Optional Linked Book)
                            </label>
                            <select name="book_id" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500">
                                <option value="">-- स्वतंत्र ऑडियोबुक (कोई ई-बुक लिंक नहीं) --</option>
                                @foreach($books as $bk)
                                    <option value="{{ $bk->id }}" {{ old('book_id') == $bk->id ? 'selected' : '' }}>
                                        {{ $bk->title }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">यदि यह ऑडियोबुक लाइब्रेरी में मौजूद किसी पुस्तक पर आधारित है, तो उसे यहाँ चुनें।</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            विवरण एवं सारांश (Description / Synopsis)
                        </label>
                        <textarea name="description" x-model="description" rows="4" placeholder="ऑडियोबुक की कथावस्तु, विशेषताएं एवं सारांश लिखें..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500">{{ old('description') }}</textarea>
                    </div>

                    <!-- Multi-chapter vs Single track choice -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="add_chapters_next" value="1" x-model="addChaptersNext" class="mt-1 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 block">यह एक बहु-अध्याय (Multi-Chapter) ऑडियोबुक है</span>
                                <span class="text-xs text-slate-500 leading-relaxed block mt-0.5">
                                    इसे चेक करने पर मुख्य विवरण सहेजने के बाद आपको अध्याय प्रबंधन (Chapters & Tracks) स्क्रीन पर भेजा जाएगा, जहाँ आप एक-एक करके सभी ट्रैक जोड़ सकेंगे।
                                </span>
                            </div>
                        </label>
                    </div>

                    <!-- Single Track Upload / External Audio (Shown optionally or as full audio) -->
                    <div class="space-y-4" x-show="!addChaptersNext">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    एकल ऑडियो फ़ाइल अपलोड (Full Audio File)
                                </label>
                                <input type="file" name="audio_file" accept=".mp3,.m4a,.aac" class="w-full text-xs text-slate-600 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200">
                                <p class="text-[11px] text-slate-400 mt-1">MP3, M4A या AAC (अधिकतम 50MB)</p>
                                @error('audio_file') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    कवर चित्र (Cover Image)
                                </label>
                                <input type="file" name="cover_image" accept="image/*" class="w-full text-xs text-slate-600 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200">
                                <p class="text-[11px] text-slate-400 mt-1">अनुशंसित: 1:1 वर्गाकार या 3:4 अनुपात</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                वैकल्पिक बाहरी ऑडियो लिंक (External Audio URL - Archive.org आदि)
                            </label>
                            <input type="url" name="external_audio_url" value="{{ old('external_audio_url') }}" placeholder="https://archive.org/details/..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
                        </div>
                    </div>

                    <!-- Cover image only if multi-chapter selected -->
                    <div x-show="addChaptersNext" style="display: none;">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            कवर चित्र (Cover Image)
                        </label>
                        <input type="file" name="cover_image" accept="image/*" class="w-full text-xs text-slate-600 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200">
                        <p class="text-[11px] text-slate-400 mt-1">अनुशंसित: 1:1 वर्गाकार या 3:4 अनुपात</p>
                    </div>

                    <div class="pt-4 border-t border-slate-200 space-y-3">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="copyright_declared" value="1" required class="mt-0.5 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <span class="text-xs text-slate-700 leading-relaxed">
                                <strong>कॉपीराइट एवं अधिकार घोषणा (Legal Declaration):</strong> मैं प्रमाणित करता/करती हूँ कि मेरे पास इस ऑडियो सामग्री के वितरण का वैध अधिकार है, अथवा यह सार्वजनिक डोमेन (Public Domain / CC-BY) के अंतर्गत है।
                            </span>
                        </label>
                        @error('copyright_declared') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <a href="{{ route('dashboard', ['tab' => 'submissions']) }}" class="px-5 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            रद्द करें
                        </a>
                        
                        <!-- Preview Button -->
                        <button type="button" @click="showPreview = true" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-sm font-bold transition flex items-center gap-1.5">
                            <x-app-icon name="eye" class="h-4 w-4" />
                            <span>पूर्वावलोकन देखें (Preview)</span>
                        </button>

                        <!-- Direct Submit Button -->
                        <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold shadow-xs transition flex items-center gap-1.5">
                            <x-app-icon name="upload" class="h-4 w-4" />
                            <span x-text="addChaptersNext ? 'सहेजें और अध्याय जोड़ें &rarr;' : 'समीक्षा हेतु सबमिट करें'"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- Submission Preview Modal (No public indexing, purely in-memory preview) -->
        <div x-show="showPreview" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;" 
             aria-modal="true" 
             role="dialog"
             x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 transition-opacity" @click="showPreview = false"></div>

                <div class="relative inline-block w-full max-w-lg p-6 my-8 text-left align-middle bg-white rounded-2xl shadow-xl transform transition-all border border-slate-200">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                        <div class="flex items-center gap-2">
                            <x-app-icon name="headphones" class="h-5 w-5 text-amber-700" />
                            <h3 class="text-base font-bold text-slate-900 font-serif">प्रस्तुति पूर्वावलोकन (Submission Preview)</h3>
                        </div>
                        <button type="button" @click="showPreview = false" class="text-slate-400 hover:text-slate-600">
                            <x-app-icon name="x-circle" class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="py-4 space-y-3 text-xs">
                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900">
                            <strong>सूचना:</strong> यह केवल आपकी प्रस्तुति का पूर्वावलोकन है। यह अभी तक प्रकाशित नहीं हुआ है।
                        </div>

                        <div>
                            <span class="text-slate-500 block">शीर्षक (Hindi):</span>
                            <span class="font-bold text-sm text-slate-900" x-text="title || 'शीर्षक दर्ज नहीं'"></span>
                        </div>

                        <div x-show="title_en">
                            <span class="text-slate-500 block">शीर्षक (English):</span>
                            <span class="italic text-slate-800" x-text="title_en"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100">
                            <div>
                                <span class="text-slate-500 block">लेखक:</span>
                                <span class="font-medium text-slate-800" x-text="author_name || 'अनिर्दिष्ट'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">वाचक:</span>
                                <span class="font-medium text-slate-800" x-text="narrator || 'अनिर्दिष्ट'"></span>
                            </div>
                        </div>

                        <div class="pt-1 border-t border-slate-100">
                            <span class="text-slate-500 block">भाषा:</span>
                            <span class="font-medium text-slate-800" x-text="languageName || 'हिंदी'"></span>
                        </div>

                        <div x-show="description" class="pt-1 border-t border-slate-100">
                            <span class="text-slate-500 block mb-1">विवरण:</span>
                            <p class="text-slate-700 bg-slate-50 p-2.5 rounded-lg border border-slate-100 max-h-32 overflow-y-auto whitespace-pre-line" x-text="description"></p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-500">
                            प्रस्तुतकर्ता श्रेय: <strong>{{ auth()->user()->name }}</strong> (आपकी ईमेल गोपनीय रहेगी)
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" @click="showPreview = false" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            संशोधन करें (Edit)
                        </button>
                        <button type="button" @click="document.getElementById('audiobookForm').submit()" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs">
                            पुष्टि करें और सबमिट करें (Confirm & Submit)
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>