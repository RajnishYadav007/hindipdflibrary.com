<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-slate-700">डैशबोर्ड</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('dashboard', ['tab' => 'submissions']) }}" class="hover:text-slate-700">मेरी प्रस्तुतियां</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-700 font-semibold">कहानी सबमिट करें</span>
                </nav>
                <h1 class="font-extrabold text-2xl text-slate-900 leading-tight font-serif">
                    नई कहानी श्रृंखला व प्रथम अध्याय (Story Series & Episode 1)
                </h1>
            </div>
            <a href="{{ route('dashboard', ['tab' => 'submissions']) }}" class="px-3.5 py-1.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                &larr; वापस जाएं
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-amber-50/25 min-h-[calc(100vh-12rem)]" 
         x-data="{ 
             storyType: '{{ old('story_type', $storyType ?? 'children') }}',
             showPreview: false,
             seriesTitle: '{{ old('series_title', '') }}',
             seriesTitleEn: '{{ old('series_title_en', '') }}',
             ageGroup: '{{ old('age_group', '6-8') }}',
             classification: '{{ old('classification', 'पौराणिक कथा (Mythological)') }}',
             categoryName: '',
             languageName: '',
             episodeTitle: '{{ old('episode_title', '') }}',
             episodeContent: `{{ old('episode_content', '') }}`,
             narrationType: '{{ old('narration_type', 'tts') }}',
             description: '{{ old('description', '') }}',
             init() {
                 let catSel = document.querySelector('select[name=category_id]');
                 if (catSel && catSel.selectedOptions[0] && catSel.value) {
                     this.categoryName = catSel.selectedOptions[0].text;
                 }
                 let langSel = document.querySelector('select[name=language_id]');
                 if (langSel && langSel.selectedOptions[0]) {
                     this.languageName = langSel.selectedOptions[0].text;
                 }
             }
         }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
                
                <!-- Story Type Selector Tabs -->
                <div class="mb-6 p-1.5 bg-slate-100/90 rounded-2xl flex items-center gap-1 border border-slate-200">
                    <button 
                        type="button" 
                        @click="storyType = 'children'"
                        :class="storyType === 'children' ? 'bg-white text-emerald-800 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="flex-1 py-2.5 px-3 rounded-xl text-xs font-medium text-center transition flex items-center justify-center gap-2 cursor-pointer">
                        <x-app-icon name="sparkles" class="h-4 w-4 text-emerald-600" />
                        <span>बाल साहित्य (Children's Story)</span>
                    </button>
                    <button 
                        type="button" 
                        @click="storyType = 'general'"
                        :class="storyType === 'general' ? 'bg-white text-emerald-800 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                        class="flex-1 py-2.5 px-3 rounded-xl text-xs font-medium text-center transition flex items-center justify-center gap-2 cursor-pointer">
                        <x-app-icon name="scroll-text" class="h-4 w-4 text-emerald-600" />
                        <span>अन्य / पौराणिक / लोक कथा (General Story)</span>
                    </button>
                </div>

                <div class="mb-6 p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200 text-xs text-emerald-950 space-y-1">
                    <div class="flex items-center gap-2 font-bold text-emerald-900">
                        <x-app-icon name="book-open" class="h-4 w-4 text-emerald-700" />
                        <span x-text="storyType === 'children' ? 'बाल साहित्य सबमिशन दिशा-निर्देश:' : 'पौराणिक व सामान्य कथा सबमिशन दिशा-निर्देश:'"></span>
                    </div>
                    <p class="leading-relaxed text-emerald-900/90" x-text="storyType === 'children' 
                        ? 'बाल कथाएं श्रृंखला (Series) एवं अध्यायों (Episodes) के प्रारूप में संग्रहित होती हैं। आप यहाँ श्रृंखला और उसका पहला अध्याय सबमिट कर सकते हैं।' 
                        : 'पौराणिक, ऐतिहासिक, लोक कथाएं एवं सामाजिक कहानियां यहाँ सबमिट की जा सकती हैं। अनुमोदन के उपरांत यह आपके नाम के साथ प्रकाशित होंगी।'"></p>
                </div>

                <form id="storyForm" action="{{ route('dashboard.submissions.story.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="story_type" :value="storyType">

                    <!-- Series Section -->
                    <div class="p-5 rounded-2xl bg-slate-50/60 border border-slate-200 space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>1. श्रृंखला विवरण (Series Details)</span>
                        </h4>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                श्रृंखला का नाम (Series Title in Hindi) *
                            </label>
                            <input type="text" name="series_title" x-model="seriesTitle" value="{{ old('series_title') }}" placeholder="जैसे: पंचतंत्र की अमर कहानियां / विक्रम बेताल" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500">
                            @error('series_title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                श्रृंखला का नाम अंग्रेज़ी में (Series Title in English - Optional)
                            </label>
                            <input type="text" name="series_title_en" x-model="seriesTitleEn" value="{{ old('series_title_en') }}" placeholder="e.g. Panchatantra Moral Tales" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Distinct Children's Age Bracket vs General Traditional Classification -->
                            <div x-show="storyType === 'children'">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    आयु वर्ग (Age Bracket) *
                                </label>
                                <select name="age_group" x-model="ageGroup" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500">
                                    <option value="all">सभी उम्र (All Ages)</option>
                                    <option value="3-5">3 - 5 वर्ष (Toddlers)</option>
                                    <option value="6-8">6 - 8 वर्ष (Early Readers)</option>
                                    <option value="9-12">9 - 12 वर्ष (Young Readers)</option>
                                </select>
                            </div>

                            <div x-show="storyType === 'general'" style="display: none;">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    कथा वर्गीकरण (Classification) *
                                </label>
                                <select name="classification" x-model="classification" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500">
                                    <option value="पौराणिक कथा (Mythological)">पौराणिक कथा (Mythological)</option>
                                    <option value="ऐतिहासिक कथा (Historical)">ऐतिहासिक कथा (Historical)</option>
                                    <option value="लोक कथा एवं दंतकथा (Folk Tale)">लोक कथा एवं दंतकथा (Folk Tale)</option>
                                    <option value="सामाजिक एवं प्रेरक (Social & Inspiring)">सामाजिक एवं प्रेरक (Social & Inspiring)</option>
                                    <option value="हास्य व व्यंग्य (Humor/Satire)">हास्य व व्यंग्य (Humor/Satire)</option>
                                    <option value="अन्य कथा विधा (Other Fiction)">अन्य कथा विधा (Other Fiction)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    श्रेणी (Category)
                                </label>
                                <select name="category_id" @change="categoryName = $event.target.selectedOptions[0] ? $event.target.selectedOptions[0].text : ''" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500">
                                    <option value="">-- श्रेणी चुनें --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    भाषा (Language) *
                                </label>
                                <select name="language_id" required @change="languageName = $event.target.selectedOptions[0] ? $event.target.selectedOptions[0].text : ''" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500">
                                    @foreach($languages as $lang)
                                        <option value="{{ $lang->id }}" {{ old('language_id') == $lang->id ? 'selected' : '' }}>{{ $lang->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                श्रृंखला परिचय (Series Synopsis)
                            </label>
                            <textarea name="description" x-model="description" rows="2" placeholder="इस कहानी संग्रह के बारे में संक्षिप्त जानकारी..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Episode 1 Section -->
                    <div class="p-5 rounded-2xl bg-slate-50/60 border border-slate-200 space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>2. प्रथम अध्याय (Episode 1 Details)</span>
                        </h4>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                अध्याय 1 का शीर्षक (Episode Title) *
                            </label>
                            <input type="text" name="episode_title" x-model="episodeTitle" value="{{ old('episode_title') }}" placeholder="जैसे: शेर और चतुर खरगोश" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500">
                            @error('episode_title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                कहानी की संपूर्ण सामग्री (Story Content) *
                            </label>
                            <textarea name="episode_content" x-model="episodeContent" rows="8" placeholder="कहानी का पूरा पाठ यहाँ लिखें..." required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm leading-relaxed font-serif">{{ old('episode_content') }}</textarea>
                            @error('episode_content') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    वाचन प्रकार (Narration Type)
                                </label>
                                <select name="narration_type" x-model="narrationType" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
                                    <option value="tts">स्वचालित ब्राउज़र आवाज़ (SpeechSynthesis TTS)</option>
                                    <option value="uploaded">अपलोड किया गया ऑडियो (Uploaded Voice)</option>
                                    <option value="none">केवल पाठ (Text Only)</option>
                                </select>
                            </div>
                            <div x-show="narrationType === 'uploaded'">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    ऑडियो वाचन फ़ाइल (MP3, M4A)
                                </label>
                                <input type="file" name="audio_file" accept=".mp3,.m4a" class="w-full text-xs text-slate-600 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200">
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 space-y-3">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="copyright_declared" value="1" required class="mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs text-slate-700 leading-relaxed">
                                <strong>कॉपीराइट घोषणा:</strong> मैं प्रमाणित करता/करती हूँ कि यह कहानी मेरी मूल रचना है अथवा सर्वाधिकार-मुक्त/लोककथा (Folk Tale / Public Domain) पर आधारित है।
                            </span>
                        </label>
                        @error('copyright_declared') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <a href="{{ route('dashboard', ['tab' => 'submissions']) }}" class="px-5 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50">रद्द करें</a>
                        
                        <!-- Preview Button -->
                        <button type="button" @click="showPreview = true" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-sm font-bold transition flex items-center gap-1.5">
                            <x-app-icon name="eye" class="h-4 w-4" />
                            <span>पूर्वावलोकन देखें (Preview)</span>
                        </button>

                        <!-- Submit Button -->
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-xs transition flex items-center gap-1.5">
                            <x-app-icon name="upload" class="h-4 w-4" />
                            <span>समीक्षा हेतु सबमिट करें</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Story Submission Preview Modal -->
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
                            <x-app-icon name="scroll-text" class="h-5 w-5 text-emerald-700" />
                            <h3 class="text-base font-bold text-slate-900 font-serif">कहानी सबमिशन पूर्वावलोकन (Preview)</h3>
                        </div>
                        <button type="button" @click="showPreview = false" class="text-slate-400 hover:text-slate-600">
                            <x-app-icon name="x-circle" class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="py-4 space-y-3 text-xs">
                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900">
                            <strong>सूचना:</strong> यह केवल आपकी प्रस्तुति का पूर्वावलोकन है। पुष्टि के बाद यह समीक्षाधीन (Pending Review) स्थिति में जाएगा।
                        </div>

                        <div>
                            <span class="text-slate-500 block">श्रृंखला शीर्षक:</span>
                            <span class="font-bold text-sm text-slate-900" x-text="seriesTitle || 'शीर्षक दर्ज नहीं'"></span>
                            <span class="italic text-slate-500 block text-[11px]" x-show="seriesTitleEn" x-text="seriesTitleEn"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100">
                            <div>
                                <span class="text-slate-500 block">विधा / वर्ग:</span>
                                <span class="font-medium text-slate-800" x-text="storyType === 'children' ? 'बाल साहित्य' : 'सामान्य/पौराणिक'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block" x-text="storyType === 'children' ? 'आयु वर्ग:' : 'वर्गीकरण:'"></span>
                                <span class="font-medium text-slate-800" x-text="storyType === 'children' ? ageGroup : classification"></span>
                            </div>
                        </div>

                        <div class="pt-1 border-t border-slate-100">
                            <span class="text-slate-500 block">प्रथम अध्याय शीर्षक:</span>
                            <span class="font-semibold text-slate-900" x-text="episodeTitle || 'अध्याय शीर्षक दर्ज नहीं'"></span>
                        </div>

                        <div x-show="episodeContent" class="pt-1 border-t border-slate-100">
                            <span class="text-slate-500 block mb-1">कहानी झलक:</span>
                            <p class="text-slate-800 bg-slate-50 p-2.5 rounded-lg border border-slate-100 max-h-28 overflow-y-auto whitespace-pre-line font-serif text-xs leading-relaxed" x-text="episodeContent"></p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-500">
                            प्रस्तुतकर्ता श्रेय: <strong>{{ auth()->user()->name }}</strong> (आपकी ईमेल गोपनीय रहेगी)
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" @click="showPreview = false" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            संशोधन करें (Edit)
                        </button>
                        <button type="button" @click="document.getElementById('storyForm').submit()" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs">
                            पुष्टि करें और सबमिट करें (Confirm & Submit)
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>