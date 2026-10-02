<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-slate-700">डैशबोर्ड</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('dashboard', ['tab' => 'books']) }}" class="hover:text-slate-700">मेरी पुस्तकें</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-700 font-semibold">नई पुस्तक जोड़ें</span>
                </nav>
                <h1 class="font-extrabold text-2xl text-slate-900 leading-tight font-serif">
                    Add a New Book - नई पुस्तक सबमिट करें
                </h1>
            </div>
            <a 
                href="{{ route('dashboard', ['tab' => 'books']) }}" 
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition self-start sm:self-auto"
            >
                &larr; वापस जाएं
            </a>
        </div>
    </x-slot>

    <div class="py-8 bg-amber-50/25 min-h-[calc(100vh-12rem)]"
         x-data="{
             showPreview: false,
             sourceType: '{{ old('source_type', 'pdf') }}',
             title: '{{ old('title', '') }}',
             title_en: '{{ old('title_en', '') }}',
             newAuthorName: '{{ old('new_author_name', '') }}',
             authorId: '{{ old('author_id', '') }}',
             authorText: '',
             categoryText: '',
             languageText: '',
             isbn: '{{ old('isbn', '') }}',
             edition: '{{ old('edition', '') }}',
             pubYear: '{{ old('publication_year', '') }}',
             pages: '{{ old('pages', '') }}',
             publisher: '{{ old('publisher', '') }}',
             description: '{{ old('description', '') }}',
             coverPreview: null,
             init() {
                 let authSel = document.getElementById('author_id');
                 if (authSel && authSel.selectedOptions[0] && authSel.value) {
                     this.authorText = authSel.selectedOptions[0].text;
                 }
                 let catSel = document.getElementById('category_id');
                 if (catSel && catSel.selectedOptions[0] && catSel.value) {
                     this.categoryText = catSel.selectedOptions[0].text;
                 }
                 let langSel = document.getElementById('original_language_id');
                 if (langSel && langSel.selectedOptions[0]) {
                     this.languageText = langSel.selectedOptions[0].text;
                 }
             },
             getResolvedAuthor() {
                 return this.newAuthorName ? this.newAuthorName : (this.authorText || 'लेखक अनिर्दिष्ट');
             }
         }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-xs">
                
                <!-- Info Banner -->
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 text-xs text-amber-900 leading-relaxed flex items-start gap-3">
                    <div class="p-1 rounded-lg bg-amber-100 text-amber-800 shrink-0 mt-0.5">
                        <x-app-icon name="book-open" class="h-4 w-4" />
                    </div>
                    <div>
                        <strong class="block mb-1">सांस्कृतिक योगदान निर्देश (Book Contribution Guidelines):</strong>
                        <p class="text-amber-800">
                            कृपया केवल वे पुस्तकें सबमिट करें जो भारतीय कॉपीराइट अधिनियम के तहत सार्वजनिक डोमेन (Public Domain) में हों या जिनके खुले वितरण की विधिक अनुमति हो। सबमिशन के बाद हमारी संपादकीय टीम द्वारा पुस्तक की समीक्षा की जाएगी।
                        </p>
                    </div>
                </div>

                <form id="bookSubmitForm" action="{{ route('dashboard.books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- 1. Title & Names -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                पुस्तक का शीर्षक (Hindi Title) <span class="text-rose-600">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="title" 
                                id="title" 
                                x-model="title"
                                value="{{ old('title') }}" 
                                required 
                                placeholder="उदा. गोदान, निर्मला, मधुशाला..." 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-amber-500 focus:ring-amber-500"
                            />
                            @error('title')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="title_en" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                English Title / Transliteration (वैकल्पिक)
                            </label>
                            <input 
                                type="text" 
                                name="title_en" 
                                id="title_en" 
                                x-model="title_en"
                                value="{{ old('title_en') }}" 
                                placeholder="e.g. Godan, Nirmala, Madhushala..." 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-amber-500 focus:ring-amber-500"
                            />
                            @error('title_en')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- 2. Author Selection or Entry -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            लेखक की जानकारी (Author) <span class="text-rose-600">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="author_id" class="block text-xs text-slate-500 mb-1">
                                    मौजूदा लेखक में से चुनें (Select Existing):
                                </label>
                                <select 
                                    name="author_id" 
                                    id="author_id" 
                                    x-model="authorId"
                                    @change="authorText = $event.target.selectedOptions[0] ? $event.target.selectedOptions[0].text : ''"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-amber-500"
                                >
                                    <option value="">-- लेखक चुनें (Select Author) --</option>
                                    @foreach ($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                                            {{ $author->translations->first()?->name ?? $author->slug }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="new_author_name" class="block text-xs text-slate-500 mb-1">
                                    अथवा नया लेखक नाम लिखें (Or Type New Author):
                                </label>
                                <input 
                                    type="text" 
                                    name="new_author_name" 
                                    id="new_author_name" 
                                    x-model="newAuthorName"
                                    value="{{ old('new_author_name') }}" 
                                    placeholder="उदा. मुंशी प्रेमचंद, हरिवंश राय बच्चन" 
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500"
                                />
                            </div>
                        </div>
                        @error('author_id')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 3. Category & Language -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="category_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                श्रेणी (Category / Genre)
                            </label>
                            <select 
                                name="category_id" 
                                id="category_id" 
                                @change="categoryText = $event.target.selectedOptions[0] ? $event.target.selectedOptions[0].text : ''"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-amber-500"
                            >
                                <option value="">-- श्रेणी चुनें (Select Category) --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->translations->first()?->name ?? $category->slug }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="original_language_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                भाषा (Language)
                            </label>
                            <select 
                                name="original_language_id" 
                                id="original_language_id" 
                                @change="languageText = $event.target.selectedOptions[0] ? $event.target.selectedOptions[0].text : ''"
                                class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-amber-500"
                            >
                                @foreach ($languages as $language)
                                    <option value="{{ $language->id }}" {{ (old('original_language_id') == $language->id || ($language->code === 'hi' && ! old('original_language_id'))) ? 'selected' : '' }}>
                                        {{ $language->native_name ?? $language->name }} ({{ $language->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('original_language_id')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- 4. Publishing & Metadata Details (Year, Pages, Publisher, ISBN, Edition) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="publication_year" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                प्रकाशन वर्ष (Year)
                            </label>
                            <input 
                                type="number" 
                                name="publication_year" 
                                id="publication_year" 
                                x-model="pubYear"
                                value="{{ old('publication_year') }}" 
                                min="1800" 
                                max="{{ date('Y') + 1 }}" 
                                placeholder="उदा. 1936" 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500"
                            />
                            @error('publication_year')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="pages" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                कुल पृष्ठ संख्या (Pages)
                            </label>
                            <input 
                                type="number" 
                                name="pages" 
                                id="pages" 
                                x-model="pages"
                                value="{{ old('pages') }}" 
                                min="1" 
                                max="5000" 
                                placeholder="उदा. 280" 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500"
                            />
                            @error('pages')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="publisher" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                प्रकाशक (Publisher)
                            </label>
                            <input 
                                type="text" 
                                name="publisher" 
                                id="publisher" 
                                x-model="publisher"
                                value="{{ old('publisher') }}" 
                                placeholder="उदा. सरस्वती प्रेस, नागरी प्रचारिणी..." 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500"
                            />
                            @error('publisher')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Metadata: ISBN and Edition -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="isbn" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                ISBN (वैकल्पिक / Optional)
                            </label>
                            <input 
                                type="text" 
                                name="isbn" 
                                id="isbn" 
                                x-model="isbn"
                                value="{{ old('isbn') }}" 
                                placeholder="e.g. 978-81-267-1234-5" 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500 font-mono"
                            />
                            @error('isbn')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="edition" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                संस्करण (Edition - उदा. प्रथम, संशोधित)
                            </label>
                            <input 
                                type="text" 
                                name="edition" 
                                id="edition" 
                                x-model="edition"
                                value="{{ old('edition') }}" 
                                placeholder="उदा. प्रथम संस्करण (1936), द्वितीय संस्करण" 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm text-slate-900 focus:border-amber-500"
                            />
                            @error('edition')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- 5. Synopsis / Description -->
                    <div>
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            पुस्तक का सारांश / परिचय (Synopsis)
                        </label>
                        <textarea 
                            name="description" 
                            id="description" 
                            x-model="description"
                            rows="4" 
                            placeholder="पुस्तक की कथावस्तु, विषय और साहित्यिक महत्व का संक्षिप्त विवरण लिखें..." 
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-amber-500"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Book Cover Image Upload -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 space-y-4">
                        <div class="flex items-center gap-2">
                            <x-app-icon name="sparkles" class="h-4 w-4 text-amber-700" />
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                                पुस्तक का मुख्य पृष्ठ / कवर चित्र (Book Cover Image)
                            </h3>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-5">
                            <div class="h-28 w-20 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-white flex items-center justify-center text-slate-400 shadow-2xs">
                                <template x-if="coverPreview">
                                    <img :src="coverPreview" alt="Cover Preview" class="h-full w-full object-cover" />
                                </template>
                                <template x-if="!coverPreview">
                                    <span class="text-[10px] text-center px-1 font-medium">Cover Preview</span>
                                </template>
                            </div>

                            <div class="flex-1 w-full">
                                <label for="cover_image" class="block text-xs font-medium text-slate-700 mb-1">
                                    कवर चित्र अपलोड करें (JPG, PNG, WebP - Max 5MB):
                                </label>
                                <input 
                                    type="file" 
                                    name="cover_image" 
                                    id="cover_image" 
                                    accept="image/jpeg,image/png,image/webp,image/jpg" 
                                    @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => coverPreview = e.target.result; reader.readAsDataURL(file); }"
                                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer"
                                />
                                <p class="text-[11px] text-slate-400 mt-1">
                                    सुझाव: 2:3 अनुपात का सुस्पष्ट चित्र (Portrait cover page) सर्वोत्तम दिखता है।
                                </p>
                                @error('cover_image')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- 6. Conditional Source Selection: PDF vs External Source -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 space-y-4">
                        <div class="flex items-center gap-2">
                            <x-app-icon name="file-text" class="h-4 w-4 text-amber-700" />
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                                पुस्तक का डिजिटल स्रोत (Digital Source Format) <span class="text-rose-600">*</span>
                            </h3>
                        </div>

                        <!-- Radio Selector -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition" 
                                   :class="sourceType === 'pdf' ? 'bg-amber-50/80 border-amber-400 shadow-xs' : 'bg-white border-slate-200 hover:border-slate-300'">
                                <input type="radio" name="source_type" value="pdf" x-model="sourceType" class="text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block">सीधे PDF फ़ाइल अपलोड करें</span>
                                    <span class="text-[11px] text-slate-500 block">फ़ाइल सर्वर पर सुरक्षित रूप से संग्रहित होगी (Max 50MB)</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition" 
                                   :class="sourceType === 'external' ? 'bg-amber-50/80 border-amber-400 shadow-xs' : 'bg-white border-slate-200 hover:border-slate-300'">
                                <input type="radio" name="source_type" value="external" x-model="sourceType" class="text-amber-600 focus:ring-amber-500">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block">ऑनलाइन स्रोत लिंक (Archive.org आदि)</span>
                                    <span class="text-[11px] text-slate-500 block">Archive.org, नेशनल डिजिटल लाइब्रेरी या वैध पब्लिक डोमेन लिंक</span>
                                </div>
                            </label>
                        </div>

                        <!-- Conditional Input for PDF File -->
                        <div x-show="sourceType === 'pdf'" class="pt-2">
                            <label for="pdf_file" class="block text-xs font-bold text-slate-700 mb-1">
                                पीडीएफ फ़ाइल अपलोड करें (Upload PDF) <span class="text-rose-600">*</span>
                            </label>
                            <input 
                                type="file" 
                                name="pdf_file" 
                                id="pdf_file" 
                                accept="application/pdf" 
                                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer"
                            />
                            <p class="text-[11px] text-slate-400 mt-1">स्वीकृत प्रारूप: PDF (अधिकतम फ़ाइल आकार: 50MB)</p>
                            @error('pdf_file')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Conditional Input for External URL -->
                        <div x-show="sourceType === 'external'" class="pt-2" style="display: none;">
                            <label for="source_url" class="block text-xs font-bold text-slate-700 mb-1">
                                डिजिटल स्रोत URL दर्ज करें (External Source URL) <span class="text-rose-600">*</span>
                            </label>
                            <input 
                                type="url" 
                                name="source_url" 
                                id="source_url" 
                                value="{{ old('source_url') }}" 
                                placeholder="https://archive.org/details/..." 
                                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-amber-500"
                            />
                            <p class="text-[11px] text-slate-400 mt-1">उदाहरण: https://archive.org/details/godan-premchand</p>
                            @error('source_url')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- 7. Copyright & Confirmation Checkbox -->
                    <div class="pt-2">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                required 
                                name="terms_confirm" 
                                id="terms_confirm" 
                                class="mt-1 h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500"
                            />
                            <span class="text-xs text-slate-700 leading-relaxed">
                                <strong>प्रतिलिप्यधिकार घोषणा:</strong> मैं प्रमाणित करता/करती हूँ कि यह पुस्तक भारतीय कॉपीराइट अधिनियम के तहत सार्वजनिक डोमेन (Public Domain) में है, अथवा मेरे पास इसे सांस्कृतिक व शैक्षणिक उपयोग हेतु साझा करने के विधिक अधिकार हैं।
                            </span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                        <a 
                            href="{{ route('dashboard', ['tab' => 'books']) }}" 
                            class="rounded-xl border border-slate-300 px-5 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                        >
                            रद्द करें (Cancel)
                        </a>
                        
                        <!-- Preview Button -->
                        <button 
                            type="button" 
                            @click="showPreview = true" 
                            class="rounded-xl bg-slate-100 hover:bg-slate-200 px-5 py-2.5 text-xs font-bold text-slate-800 transition flex items-center gap-1.5"
                        >
                            <x-app-icon name="eye" class="h-4 w-4" />
                            <span>पूर्वावलोकन देखें (Preview)</span>
                        </button>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="rounded-xl bg-amber-600 hover:bg-amber-700 px-6 py-2.5 text-xs font-bold text-white shadow-xs transition flex items-center gap-1.5"
                        >
                            <x-app-icon name="upload" class="h-4 w-4" />
                            <span>पुस्तक सबमिट करें (Submit Book)</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Book Submission Preview Modal -->
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
                            <x-app-icon name="book-open" class="h-5 w-5 text-amber-700" />
                            <h3 class="text-base font-bold text-slate-900 font-serif">पुस्तक सबमिशन पूर्वावलोकन (Preview)</h3>
                        </div>
                        <button type="button" @click="showPreview = false" class="text-slate-400 hover:text-slate-600">
                            <x-app-icon name="x-circle" class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="py-4 space-y-3 text-xs">
                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900">
                            <strong>सूचना:</strong> यह केवल आपकी प्रस्तुति का पूर्वावलोकन है। पुष्टि के बाद यह संपादन समीक्षा (Pending Review) स्थिति में जाएगा।
                        </div>

                        <div>
                            <span class="text-slate-500 block">पुस्तक शीर्षक:</span>
                            <span class="font-bold text-sm text-slate-900" x-text="title || 'शीर्षक दर्ज नहीं'"></span>
                            <span class="italic text-slate-500 block text-[11px]" x-show="title_en" x-text="title_en"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100">
                            <div>
                                <span class="text-slate-500 block">लेखक:</span>
                                <span class="font-medium text-slate-800" x-text="getResolvedAuthor()"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">श्रेणी:</span>
                                <span class="font-medium text-slate-800" x-text="categoryText || 'अवर्गीकृत'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100">
                            <div>
                                <span class="text-slate-500 block">स्रोत प्रारूप:</span>
                                <span class="font-medium text-slate-800" x-text="sourceType === 'pdf' ? 'सीधे PDF फ़ाइल' : 'बाहरी स्रोत लिंक'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">भाषा:</span>
                                <span class="font-medium text-slate-800" x-text="languageText || 'हिंदी'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100" x-show="isbn || edition">
                            <div x-show="isbn">
                                <span class="text-slate-500 block">ISBN:</span>
                                <span class="font-mono text-slate-800" x-text="isbn"></span>
                            </div>
                            <div x-show="edition">
                                <span class="text-slate-500 block">संस्करण:</span>
                                <span class="font-medium text-slate-800" x-text="edition"></span>
                            </div>
                        </div>

                        <div x-show="description" class="pt-1 border-t border-slate-100">
                            <span class="text-slate-500 block mb-1">विवरण:</span>
                            <p class="text-slate-700 bg-slate-50 p-2.5 rounded-lg border border-slate-100 max-h-28 overflow-y-auto whitespace-pre-line" x-text="description"></p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 text-[11px] text-slate-500">
                            प्रस्तुतकर्ता श्रेय: <strong>{{ auth()->user()->name }}</strong> (आपकी ईमेल गोपनीय रहेगी)
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" @click="showPreview = false" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            संशोधन करें (Edit)
                        </button>
                        <button type="button" @click="document.getElementById('bookSubmitForm').submit()" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs">
                            पुष्टि करें और सबमिट करें (Confirm & Submit)
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>