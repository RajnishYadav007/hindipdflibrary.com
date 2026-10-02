<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-slate-700">डैशबोर्ड</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('dashboard', ['tab' => 'submissions']) }}" class="hover:text-slate-700">मेरी प्रस्तुतियां</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('dashboard.submissions.show', ['audiobook', $audiobook->id]) }}" class="hover:text-slate-700 truncate max-w-[150px]">{{ $audiobook->title }}</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-700 font-semibold">अध्याय प्रबंधन</span>
                </nav>
                <h1 class="text-2xl font-extrabold text-slate-900 font-serif">
                    अध्याय एवं ट्रैक प्रबंधन: {{ $audiobook->title }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    कुल अवधि: <span class="font-medium text-slate-700">{{ $audiobook->formatted_duration ?? 'गणना जारी / अनिर्दिष्ट' }}</span> &bull; 
                    कुल अध्याय: <span class="font-medium text-slate-700">{{ $audiobook->chapters->count() }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.submissions.show', ['audiobook', $audiobook->id]) }}" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50">
                    &larr; प्रस्तुति विवरण पर लौटें
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-amber-50/25 min-h-[calc(100vh-12rem)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-medium flex items-center gap-2">
                    <x-app-icon name="check-circle" class="h-4 w-4 text-emerald-600 shrink-0" />
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <x-app-icon name="x-circle" class="h-4 w-4 text-rose-600 shrink-0" />
                        <span>कृपया फॉर्म की त्रुटियों को सुधारें:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Info Notice -->
            <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 text-xs text-amber-900 flex items-start gap-3">
                <div class="p-1.5 rounded-lg bg-amber-100 text-amber-800 shrink-0 mt-0.5">
                    <x-app-icon name="music" class="h-4 w-4" />
                </div>
                <div class="space-y-1 leading-relaxed">
                    <p class="font-bold">मल्टी-चैप्टर ट्रैक निर्देश (Multi-Track Guidelines):</p>
                    <p>यदि यह ऑडियोबुक विभिन्न अध्यायों, खंडों या कड़ियों में विभाजित है, तो आप नीचे से क्रमवार नए अध्याय जोड़ सकते हैं। प्रत्येक अध्याय के लिए अलग ऑडियो फ़ाइल (MP3, M4A) या बाहरी ऑडियो लिंक मान्य है।</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <!-- Left Column: Existing Chapters List (7 Cols) -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <x-app-icon name="headphones" class="h-4 w-4 text-amber-700" />
                                <span>वर्तमान अध्याय सूची ({{ $audiobook->chapters->count() }})</span>
                            </h2>
                            <span class="text-xs text-slate-500">क्रमबद्ध सूची</span>
                        </div>

                        @if ($audiobook->chapters->isNotEmpty())
                            <div class="space-y-3">
                                @foreach ($audiobook->chapters->sortBy('chapter_number') as $ch)
                                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3">
                                                <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-900 font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                                    {{ $ch->chapter_number }}
                                                </span>
                                                <div>
                                                    <h3 class="text-sm font-bold text-slate-900">{{ $ch->title }}</h3>
                                                    @if (!empty($ch->title_en))
                                                        <p class="text-xs text-slate-500 italic">{{ $ch->title_en }}</p>
                                                    @endif
                                                    <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                                                        <span>अवधि: <strong class="text-slate-700">{{ $ch->formatted_duration ?? 'अनिर्दिष्ट' }}</strong></span>
                                                        @if ($ch->external_audio_url)
                                                            <span class="text-amber-700 font-medium">&bull; बाहरी लिंक</span>
                                                        @elseif ($ch->audio_stream_url)
                                                            <span class="text-emerald-700 font-medium">&bull; होस्टेड ऑडियो</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            @if (!$audiobook->isPublished())
                                                <form action="{{ route('dashboard.submissions.audiobook.chapters.destroy', [$audiobook, $ch]) }}" method="POST" onsubmit="return confirm('क्या आप वाकई अध्याय {{ $ch->chapter_number }} हटाना चाहते हैं?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="अध्याय हटाएं">
                                                        <x-app-icon name="trash" class="h-4 w-4" />
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- Audio Player Preview -->
                                        @if ($ch->audio_stream_url)
                                            <div class="mt-3 pt-3 border-t border-slate-200/60">
                                                <audio controls class="w-full h-8 rounded">
                                                    <source src="{{ $ch->audio_stream_url }}" type="audio/mpeg">
                                                    ब्राउज़र सपोर्ट नहीं करता।
                                                </audio>
                                            </div>
                                        @elseif ($ch->external_audio_url)
                                            <div class="mt-2 pt-2 border-t border-slate-200/60">
                                                <a href="{{ $ch->external_audio_url }}" target="_blank" rel="noopener noreferrer" class="text-xs text-amber-700 hover:underline flex items-center gap-1 break-all">
                                                    <span>{{ $ch->external_audio_url }}</span>
                                                    <x-app-icon name="external-link" class="h-3 w-3 inline shrink-0" />
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <x-empty-state 
                                icon="music"
                                title="कोई अध्याय मौजूद नहीं है"
                                description="इस ऑडियोबुक में अभी तक अलग-अलग अध्याय नहीं जोड़े गए हैं। दाईं ओर दिए गए फॉर्म से पहला अध्याय जोड़ें।"
                            />
                        @endif
                    </div>
                </div>

                <!-- Right Column: Add Chapter Form (5 Cols) -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs sticky top-6">
                        <h2 class="text-sm font-bold text-slate-900 mb-1 flex items-center gap-1.5">
                            <x-app-icon name="plus" class="h-4 w-4 text-amber-600" />
                            <span>नया अध्याय / ट्रैक जोड़ें (Add Chapter)</span>
                        </h2>
                        <p class="text-xs text-slate-500 mb-4">प्रत्येक ट्रैक की फ़ाइल या बाहरी लिंक दर्ज करें</p>

                        @if ($audiobook->isPublished())
                            <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                                यह ऑडियोबुक पहले ही प्रकाशित हो चुकी है। नए अध्याय जोड़ने या संपादन हेतु कृपया एडमिन से संपर्क करें।
                            </div>
                        @else
                            <form action="{{ route('dashboard.submissions.audiobook.chapters.store', $audiobook) }}" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="{ sourceMode: 'upload' }">
                                @csrf

                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                            क्रम # *
                                        </label>
                                        <input type="number" name="chapter_number" value="{{ old('chapter_number', ($audiobook->chapters->max('chapter_number') ?? 0) + 1) }}" min="1" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-amber-500">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                            अवधि (सेकंड - वैकल्पिक)
                                        </label>
                                        <input type="number" name="duration_seconds" value="{{ old('duration_seconds') }}" placeholder="उदा. 360" min="0" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-amber-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        अध्याय शीर्षक (Hindi) *
                                    </label>
                                    <input type="text" name="title" value="{{ old('title') }}" placeholder="उदा. अध्याय 1: आरंभ" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-amber-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        अध्याय शीर्षक (English)
                                    </label>
                                    <input type="text" name="title_en" value="{{ old('title_en') }}" placeholder="e.g. Chapter 1: The Beginning" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-amber-500">
                                </div>

                                <!-- Audio Source Selector -->
                                <div class="pt-2">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                        ऑडियो स्रोत (Audio Source) *
                                    </label>
                                    <div class="grid grid-cols-2 gap-2 mb-3">
                                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer text-xs transition" :class="sourceMode === 'upload' ? 'bg-amber-50 border-amber-300 text-amber-900 font-bold' : 'border-slate-200 text-slate-600'">
                                            <input type="radio" name="source_mode_radio" value="upload" x-model="sourceMode" class="text-amber-600 focus:ring-amber-500">
                                            <span>फ़ाइल अपलोड करें</span>
                                        </label>
                                        <label class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer text-xs transition" :class="sourceMode === 'external' ? 'bg-amber-50 border-amber-300 text-amber-900 font-bold' : 'border-slate-200 text-slate-600'">
                                            <input type="radio" name="source_mode_radio" value="external" x-model="sourceMode" class="text-amber-600 focus:ring-amber-500">
                                            <span>बाहरी URL लिंक</span>
                                        </label>
                                    </div>

                                    <!-- File Upload -->
                                    <div x-show="sourceMode === 'upload'" class="space-y-1">
                                        <input type="file" name="audio_file" accept=".mp3,.m4a,.aac" class="w-full text-xs text-slate-600 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200">
                                        <p class="text-[11px] text-slate-400">MP3, M4A या AAC (अधिकतम 50MB)</p>
                                    </div>

                                    <!-- External URL -->
                                    <div x-show="sourceMode === 'external'" class="space-y-1" style="display: none;">
                                        <input type="url" name="external_audio_url" value="{{ old('external_audio_url') }}" placeholder="https://archive.org/download/..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-amber-500">
                                        <p class="text-[11px] text-slate-400">Archive.org या सीधे ऑडियो MP3/M4A URL</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        संक्षिप्त विवरण (वैकल्पिक)
                                    </label>
                                    <textarea name="description" rows="2" placeholder="इस अध्याय का संक्षिप्त सारांश..." class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs focus:border-amber-500">{{ old('description') }}</textarea>
                                </div>

                                <div class="pt-2">
                                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
                                        <x-app-icon name="plus" class="h-4 w-4" />
                                        <span>यह अध्याय जोड़ें (Save Chapter)</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>