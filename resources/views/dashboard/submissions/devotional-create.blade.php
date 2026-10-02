<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-slate-700">डैशबोर्ड</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('dashboard', ['tab' => 'submissions']) }}" class="hover:text-slate-700">मेरी प्रस्तुतियां</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-700 font-semibold">नई {{ $type === 'chalisa' ? 'चालीसा' : 'आरती' }} जोड़ें</span>
                </nav>
                <h1 class="font-extrabold text-2xl text-slate-900 leading-tight font-serif">
                    नई {{ $type === 'chalisa' ? 'चालीसा (Submit Chalisa)' : 'आरती (Submit Aarti)' }} जोड़ें
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
             deitySelect: '{{ old('deity_name', 'श्री हनुमान') }}',
             customDeity: '',
             author_name: '{{ old('author_name', '') }}',
             content: `{{ old('content', '') }}`,
             description: '{{ old('description', '') }}',
             languageName: '',
             init() {
                 let knownDeities = ['श्री हनुमान', 'भगवान शिव', 'श्री गणेश', 'माँ दुर्गा', 'माँ लक्ष्मी', 'माँ सरस्वती', 'भगवान श्री राम', 'भगवान श्री कृष्ण', 'भगवान सूर्य देव', 'भगवान विष्णु', 'शनि देव', 'माँ गायत्री'];
                 if (this.deitySelect && !knownDeities.includes(this.deitySelect)) {
                     this.customDeity = this.deitySelect;
                     this.deitySelect = 'other';
                 }
                 let sel = document.querySelector('select[name=language_id]');
                 if (sel && sel.selectedOptions[0]) {
                     this.languageName = sel.selectedOptions[0].text;
                 }
             },
             getFinalDeity() {
                 return this.deitySelect === 'other' ? (this.customDeity || 'अन्य') : this.deitySelect;
             }
         }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
                
                <!-- TTS Information Notice -->
                <div class="mb-6 p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 text-xs text-amber-950 space-y-2">
                    <div class="flex items-center gap-2 font-bold text-amber-900">
                        <x-app-icon name="flame" class="h-4 w-4 text-amber-700" />
                        <span>धार्मिक पाठ सबमिशन एवं ऑडियो (TTS) सुविधा:</span>
                    </div>
                    <p class="leading-relaxed text-amber-900/90">
                        कृपया पद्यों, दोहों एवं चौपाइयों को शुद्ध वर्तनी में प्रविष्ट करें। 
                        <strong>ऑडियो फ़ाइल अपलोड करने की आवश्यकता नहीं है</strong> — सबमिशन के पश्चात यह स्वचालित रूप से हमारे ब्राउज़र-नेटिव टेक्स्ट-टू-स्पीच (TTS) प्लेयर द्वारा पाठकों को ध्वनि रूप में सुनने योग्य उपलब्ध होगी।
                    </p>
                </div>

                <form id="devotionalForm" action="{{ route('dashboard.submissions.devotional.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            {{ $type === 'chalisa' ? 'चालीसा' : 'आरती' }} का शीर्षक (Title in Hindi) *
                        </label>
                        <input type="text" name="title" x-model="title" value="{{ old('title') }}" placeholder="{{ $type === 'chalisa' ? 'जैसे: श्री हनुमान चालीसा' : 'जैसे: आरती कीजै हनुमान लला की' }}" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500 focus:ring-amber-500">
                        @error('title') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Curated Deity Dropdown + Other Input -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                आराध्य देव / देवी (Deity) *
                            </label>
                            <select x-model="deitySelect" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500 mb-2">
                                <option value="श्री हनुमान">श्री हनुमान (Shree Hanuman)</option>
                                <option value="भगवान शिव">भगवान शिव (Lord Shiva)</option>
                                <option value="श्री गणेश">श्री गणेश (Lord Ganesha)</option>
                                <option value="माँ दुर्गा">माँ दुर्गा (Maa Durga)</option>
                                <option value="माँ लक्ष्मी">माँ लक्ष्मी (Maa Lakshmi)</option>
                                <option value="माँ सरस्वती">माँ सरस्वती (Maa Saraswati)</option>
                                <option value="भगवान श्री राम">भगवान श्री राम (Lord Rama)</option>
                                <option value="भगवान श्री कृष्ण">भगवान श्री कृष्ण (Lord Krishna)</option>
                                <option value="भगवान सूर्य देव">भगवान सूर्य देव (Surya Dev)</option>
                                <option value="भगवान विष्णु">भगवान विष्णु (Lord Vishnu)</option>
                                <option value="शनि देव">शनि देव (Shani Dev)</option>
                                <option value="माँ गायत्री">माँ गायत्री (Maa Gayatri)</option>
                                <option value="other">अन्य आराध्य देव/देवी (Other - लिखें)</option>
                            </select>

                            <!-- Hidden field submitted to controller with final deity name -->
                            <input type="hidden" name="deity_name" :value="getFinalDeity()">

                            <!-- Custom Deity input if 'other' is selected -->
                            <div x-show="deitySelect === 'other'" style="display: none;">
                                <input type="text" x-model="customDeity" placeholder="आराध्य देव या देवी का नाम लिखें..." class="w-full rounded-xl border border-amber-300 bg-amber-50/40 px-3 py-2 text-xs focus:border-amber-500">
                            </div>
                            @error('deity_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                रचयिता / कवि (Author / Saint - यदि ज्ञात हो)
                            </label>
                            <input type="text" name="author_name" x-model="author_name" value="{{ old('author_name') }}" placeholder="जैसे: गोस्वामी तुलसीदास" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500">
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
                                अंग्रेज़ी शीर्षक (Title in English)
                            </label>
                            <input type="text" name="title_en" x-model="title_en" value="{{ old('title_en') }}" placeholder="e.g. Shree Hanuman Chalisa" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            सम्पूर्ण पाठ / चौपाई / पद (Full Verses / Stanzas) *
                        </label>
                        <textarea name="content" x-model="content" rows="12" placeholder="दोहा:&#10;श्रीगुरु चरन सरोज रज, निज मनु मुकुरु सुधारि...&#10;&#10;चौपाई:&#10;जय हनुमान ज्ञान गुन सागर..." required class="w-full font-serif rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-amber-500 leading-relaxed">{{ old('content') }}</textarea>
                        @error('content') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            संक्षिप्त परिचय व महिमा (Description / Significance - वैकल्पिक)
                        </label>
                        <textarea name="description" x-model="description" rows="3" placeholder="इस पाठ का धार्मिक महत्व एवं पाठ विधि..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-amber-500">{{ old('description') }}</textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-200 space-y-3">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="copyright_declared" value="1" required class="mt-0.5 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <span class="text-xs text-slate-700 leading-relaxed">
                                <strong>प्रतिलिप्यधिकार घोषणा:</strong> यह पाठ पारम्परिक सनातन सार्वजनिक धरोहर (Public Domain) है और समाज के धार्मिक एवं सांस्कृतिक लाभ हेतु प्रस्तुत किया जा रहा है।
                            </span>
                        </label>
                        @error('copyright_declared') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <a href="{{ route('dashboard', ['tab' => 'submissions']) }}" class="px-5 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50">रद्द करें</a>
                        
                        <!-- Preview Button -->
                        <button type="button" @click="showPreview = true" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-sm font-bold transition flex items-center gap-1.5">
                            <x-app-icon name="eye" class="h-4 w-4" />
                            <span>पूर्वावलोकन देखें (Preview)</span>
                        </button>

                        <!-- Submit Button -->
                        <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold shadow-xs transition flex items-center gap-1.5">
                            <x-app-icon name="upload" class="h-4 w-4" />
                            <span>समीक्षा हेतु सबमिट करें</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Devotional Submission Preview Modal -->
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
                            <x-app-icon name="flame" class="h-5 w-5 text-amber-700" />
                            <h3 class="text-base font-bold text-slate-900 font-serif">प्रस्तुति पूर्वावलोकन (Preview)</h3>
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
                            <span class="text-slate-500 block">शीर्षक:</span>
                            <span class="font-bold text-sm text-slate-900" x-text="title || 'शीर्षक दर्ज नहीं'"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100">
                            <div>
                                <span class="text-slate-500 block">आराध्य देव / देवी:</span>
                                <span class="font-medium text-slate-800" x-text="getFinalDeity()"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">रचयिता:</span>
                                <span class="font-medium text-slate-800" x-text="author_name || 'पारम्परिक / अज्ञात'"></span>
                            </div>
                        </div>

                        <div class="pt-1 border-t border-slate-100">
                            <span class="text-slate-500 block">भाषा:</span>
                            <span class="font-medium text-slate-800" x-text="languageName || 'हिंदी'"></span>
                        </div>

                        <div x-show="content" class="pt-1 border-t border-slate-100">
                            <span class="text-slate-500 block mb-1">पाठ सामग्री (झलक):</span>
                            <pre class="text-slate-800 bg-amber-50/40 p-3 rounded-lg border border-amber-100 max-h-36 overflow-y-auto whitespace-pre-wrap font-serif text-xs leading-relaxed" x-text="content"></pre>
                        </div>

                        <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-500">
                            प्रस्तुतकर्ता श्रेय: <strong>{{ auth()->user()->name }}</strong> (आपकी ईमेल गोपनीय रहेगी)
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" @click="showPreview = false" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            संशोधन करें (Edit)
                        </button>
                        <button type="button" @click="document.getElementById('devotionalForm').submit()" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs">
                            पुष्टि करें और सबमिट करें (Confirm & Submit)
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>