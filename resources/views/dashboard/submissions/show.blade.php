<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-2">
                    <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition">डैशबोर्ड (Dashboard)</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('dashboard', ['tab' => 'submissions']) }}" class="hover:text-slate-700 transition">मेरी प्रस्तुतियां</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-700 font-semibold truncate max-w-[200px]">{{ $item->title ?? 'प्रस्तुति' }}</span>
                </nav>

                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-0.5 text-xs font-semibold text-slate-700 border border-slate-200">
                        <x-app-icon :name="$typeConfig['icon']" class="h-3.5 w-3.5 text-slate-600" />
                        <span>{{ $typeConfig['name'] }}</span>
                    </span>

                    @php
                        $statusStr = is_object($item->status) ? ($item->status->value ?? (string) $item->status) : (string) $item->status;
                    @endphp

                    @if ($statusStr === 'published' || (method_exists($item, 'isPublished') && $item->isPublished()))
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">
                            <x-app-icon name="check-circle" class="h-3.5 w-3.5" />
                            <span>प्रकाशित (Live)</span>
                        </span>
                    @elseif ($statusStr === 'revision_required')
                        <span class="inline-flex items-center gap-1 rounded-full bg-orange-50 px-2.5 py-0.5 text-xs font-semibold text-orange-700 border border-orange-200">
                            <x-app-icon name="alert-circle" class="h-3.5 w-3.5" />
                            <span>संशोधन अपेक्षित (Revision Required)</span>
                        </span>
                    @elseif ($statusStr === 'rejected')
                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-semibold text-rose-700 border border-rose-200">
                            <x-app-icon name="x-circle" class="h-3.5 w-3.5" />
                            <span>अस्वीकृत (Rejected)</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 border border-amber-200">
                            <x-app-icon name="clock" class="h-3.5 w-3.5" />
                            <span>समीक्षाधीन (Under Review)</span>
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 font-serif">
                    {{ $item->title }}
                </h1>
                @if (!empty($item->title_en))
                    <p class="text-sm text-slate-500 italic mt-0.5">{{ $item->title_en }}</p>
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if ($type === 'audiobook')
                    <a href="{{ route('dashboard.submissions.audiobook.chapters', $item) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                        <x-app-icon name="music" class="h-4 w-4" />
                        <span>अध्याय प्रबंधित करें (Chapters)</span>
                    </a>
                @endif

                @if ($publicUrl)
                    <a href="{{ $publicUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                        <x-app-icon name="external-link" class="h-4 w-4" />
                        <span>वेबसाइट पर देखें</span>
                    </a>
                @endif

                <a href="{{ route('dashboard', ['tab' => 'submissions']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 text-xs font-semibold rounded-xl transition">
                    &larr; <span>वापस जाएं</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-amber-50/25 min-h-[calc(100vh-12rem)]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Alerts -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-medium flex items-center gap-2">
                    <x-app-icon name="check-circle" class="h-4 w-4 text-emerald-600 shrink-0" />
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- 1. Submission Status Timeline Component -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">प्रस्तुति स्थिति (Submission Lifecycle)</h2>
                <x-submission-timeline :item="$item" :type="$type" />
            </div>

            <!-- 2. Editorial Moderation Feedback Callout (Only if revision_required or rejected) -->
            @if ($statusStr === 'revision_required' || $statusStr === 'rejected')
                <div class="rounded-2xl border p-5 sm:p-6 shadow-xs {{ $statusStr === 'rejected' ? 'bg-rose-50/70 border-rose-200' : 'bg-orange-50/70 border-orange-200' }}">
                    <div class="flex items-start gap-3">
                        <div class="p-2 rounded-xl {{ $statusStr === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-orange-100 text-orange-700' }} shrink-0">
                            <x-app-icon :name="$statusStr === 'rejected' ? 'x-circle' : 'alert-circle'" class="h-5 w-5" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold {{ $statusStr === 'rejected' ? 'text-rose-900' : 'text-orange-900' }}">
                                @if ($statusStr === 'rejected')
                                    संपादकीय समीक्षा: अस्वीकृत होने का कारण (Reason for Rejection)
                                @else
                                    संपादकीय समीक्षा: सुधार एवं संशोधन का सुझाव (Revision Needed)
                                @endif
                            </h3>
                            <p class="text-xs {{ $statusStr === 'rejected' ? 'text-rose-800' : 'text-orange-800' }} mt-1 leading-relaxed whitespace-pre-line font-medium">
                                {{ $item->admin_notes ?: ($statusStr === 'rejected' ? 'यह प्रस्तुति हमारे समुदाय दिशा-निर्देशों अथवा कॉपीराइट नीतियों के अनुरूप नहीं है।' : 'कृपया प्रदान की गई जानकारी अथवा फ़ाइल की समीक्षा कर आवश्यक सुधार करें।') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 3. Grid: Metadata Breakdown & Content Preview -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left 2 Cols: Main Content Details & Preview -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Content Details Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">सामग्री विवरण (Content Details)</h2>

                        <div class="space-y-4">
                            @if (!empty($item->description))
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-600 mb-1">विवरण / सार (Description):</h4>
                                    <div class="text-sm text-slate-800 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100 whitespace-pre-line">
                                        {{ $item->description }}
                                    </div>
                                </div>
                            @endif

                            <!-- Audio Preview if Audiobook -->
                            @if ($type === 'audiobook')
                                <div class="p-4 rounded-xl bg-amber-50/50 border border-amber-200/70">
                                    <h4 class="text-xs font-bold text-amber-900 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <x-app-icon name="headphones" class="h-4 w-4 text-amber-700" />
                                        <span>मुख्य ऑडियो ट्रैक (Main Audio Track)</span>
                                    </h4>

                                    @if ($item->audio_stream_url)
                                        <div class="mt-2">
                                            <audio controls class="w-full h-10 rounded-lg">
                                                <source src="{{ $item->audio_stream_url }}" type="audio/mpeg">
                                                आपका ब्राउज़र ऑडियो प्लेयर को सपोर्ट नहीं करता।
                                            </audio>
                                        </div>
                                    @elseif (!empty($item->external_audio_url))
                                        <p class="text-xs text-slate-600 mt-1">
                                            <strong>बाहरी ऑडियो लिंक:</strong>
                                            <a href="{{ $item->external_audio_url }}" target="_blank" rel="noopener noreferrer" class="text-amber-700 underline break-all hover:text-amber-900">
                                                {{ $item->external_audio_url }}
                                            </a>
                                        </p>
                                    @else
                                        <p class="text-xs text-slate-500 italic mt-1">कोई एकल ऑडियो फ़ाइल अपलोड नहीं की गई है। नीचे अध्याय देखें।</p>
                                    @endif
                                </div>

                                <!-- Chapters Overview -->
                                <div class="border border-slate-200 rounded-xl p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                            <x-app-icon name="music" class="h-4 w-4 text-slate-600" />
                                            <span>अध्याय सूची (Chapters): {{ $item->chapters->count() }} अध्याय</span>
                                        </h4>
                                        <a href="{{ route('dashboard.submissions.audiobook.chapters', $item) }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800">
                                            प्रबंधित करें &rarr;
                                        </a>
                                    </div>

                                    @if ($item->chapters->isNotEmpty())
                                        <div class="divide-y divide-slate-100 max-h-56 overflow-y-auto">
                                            @foreach ($item->chapters->sortBy('chapter_number') as $chapter)
                                                <div class="py-2.5 flex items-center justify-between text-xs">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-[10px]">
                                                            {{ $chapter->chapter_number }}
                                                        </span>
                                                        <span class="font-medium text-slate-800">{{ $chapter->title }}</span>
                                                    </div>
                                                    <span class="text-slate-500">{{ $chapter->formatted_duration ?? 'समय अनिर्दिष्ट' }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-500 italic">इस ऑडियोबुक में अभी तक कोई अध्याय नहीं जोड़े गए हैं।</p>
                                    @endif
                                </div>
                            @endif

                            <!-- Story Episodes Preview if Story -->
                            @if ($type === 'story')
                                <div class="border border-slate-200 rounded-xl p-4">
                                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <x-app-icon name="scroll-text" class="h-4 w-4 text-slate-600" />
                                        <span>कहानी के भाग / कड़ी (Episodes): {{ $item->episodes->count() }}</span>
                                    </h4>

                                    @if ($item->episodes->isNotEmpty())
                                        <div class="divide-y divide-slate-100 max-h-56 overflow-y-auto">
                                            @foreach ($item->episodes->sortBy('episode_number') as $ep)
                                                <div class="py-2.5 flex items-center justify-between text-xs">
                                                    <span class="font-medium text-slate-800">भाग {{ $ep->episode_number }}: {{ $ep->title }}</span>
                                                    <span class="text-slate-500">{{ $ep->reading_time_minutes ? $ep->reading_time_minutes . ' मिनट' : '' }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-500 italic">इस श्रृंखला में अभी तक कोई कड़ी दर्ज नहीं है।</p>
                                    @endif
                                </div>
                            @endif

                            <!-- Devotional Text Preview -->
                            @if ($type === 'devotional' && !empty($item->content))
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-600 mb-1">पाठ / श्लोक सामग्री (Text Content):</h4>
                                    <div class="text-sm text-slate-800 leading-relaxed bg-amber-50/30 p-4 rounded-xl border border-amber-100 font-serif max-h-72 overflow-y-auto whitespace-pre-line">
                                        {!! strip_tags($item->content, '<p><br><strong><em><h3><h4><ul><ol><li>') !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Book Source URL if present -->
                            @if (!empty($item->source_url))
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-600 mb-1">स्रोत लिंक (Source URL):</h4>
                                    <a href="{{ $item->source_url }}" target="_blank" rel="noopener noreferrer" class="text-xs text-amber-700 underline break-all hover:text-amber-900 flex items-center gap-1">
                                        <span>{{ $item->source_url }}</span>
                                        <x-app-icon name="external-link" class="h-3 w-3 inline shrink-0" />
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Attribution & Rights Preview Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">प्रतिलिप्यधिकार एवं श्रेय (Attribution & Rights)</h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-slate-500 block">कॉपीराइट स्थिति:</span>
                                <span class="font-semibold text-slate-800">
                                    {{ $item->copyright_status ?? 'घोषित (Declared by contributor)' }}
                                </span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-slate-500 block">अधिकार धारक / लाइसेंस:</span>
                                <span class="font-semibold text-slate-800">
                                    {{ $item->rights_holder ?? 'पब्लिक डोमेन / सीसी' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 p-3.5 rounded-xl bg-amber-50/50 border border-amber-200 text-xs text-amber-900 leading-relaxed">
                            <strong>प्रस्तुतकर्ता श्रेय:</strong> यह सामग्री आपके यूज़रनेम (<strong>{{ auth()->user()->name }}</strong>) के साथ श्रेयित रहेगी। किसी भी परिस्थिति में आपकी ईमेल या निजी पहचान सार्वजनिक नहीं की जाती।
                        </div>
                    </div>

                </div>

                <!-- Right 1 Col: Metadata Summary Card -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs">
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">मेटाडेटा विवरण (Metadata)</h2>

                        <div class="space-y-3.5 text-xs">
                            <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                <span class="text-slate-500">प्रारूप / प्रकार:</span>
                                <span class="font-bold text-slate-800">{{ $typeConfig['name'] }}</span>
                            </div>

                            @if (!empty($item->author_name) || !empty($item->author))
                                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">लेखक / रचयिता:</span>
                                    <span class="font-semibold text-slate-800">{{ $item->author_name ?? $item->author?->name ?? 'अज्ञात' }}</span>
                                </div>
                            @endif

                            @if (!empty($item->narrator))
                                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">वाचक / स्वर:</span>
                                    <span class="font-semibold text-slate-800">{{ $item->narrator }}</span>
                                </div>
                            @endif

                            @if (!empty($item->deity))
                                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">आराध्य देव / देवी:</span>
                                    <span class="font-semibold text-slate-800">{{ $item->deity }}</span>
                                </div>
                            @endif

                            @if (!empty($item->story_type))
                                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">कहानी वर्ग:</span>
                                    <span class="font-semibold text-slate-800">
                                        {{ $item->story_type === 'children' ? "बाल साहित्य (Children's Story)" : 'सामान्य कहानी' }}
                                    </span>
                                </div>
                            @endif

                            @if (!empty($item->classification))
                                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">वर्गीकरण / आयु वर्ग:</span>
                                    <span class="font-semibold text-slate-800">{{ $item->classification }}</span>
                                </div>
                            @endif

                            @if ($item->language)
                                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">भाषा:</span>
                                    <span class="font-semibold text-slate-800">{{ $item->language->name }}</span>
                                </div>
                            @endif

                            @if (!empty($item->formatted_duration))
                                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">कुल अवधि:</span>
                                    <span class="font-semibold text-slate-800">{{ $item->formatted_duration }}</span>
                                </div>
                            @endif

                            @if (!empty($item->isbn) || !empty($item->isbn13))
                                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">ISBN:</span>
                                    <span class="font-mono text-slate-800">{{ $item->isbn ?? $item->isbn13 }}</span>
                                </div>
                            @endif

                            @if (!empty($item->edition))
                                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">संस्करण (Edition):</span>
                                    <span class="font-semibold text-slate-800">{{ $item->edition }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                <span class="text-slate-500">योगदानकर्ता (Contributor):</span>
                                <span class="font-semibold text-slate-800">{{ $item->user?->name ?? $item->submitter?->name ?? 'योगदानकर्ता' }}</span>
                            </div>

                            <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                <span class="text-slate-500">सबमिशन दिनांक:</span>
                                <span class="text-slate-700">{{ $item->created_at?->format('d M Y, h:i A') ?? 'अज्ञात' }}</span>
                            </div>

                            @if ($item->approved_at)
                                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">सत्यापन दिनांक:</span>
                                    <span class="text-slate-700">{{ $item->approved_at->format('d M Y, h:i A') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Actions box -->
                        <div class="mt-6 pt-4 border-t border-slate-100 space-y-2">
                            @if ($type === 'audiobook')
                                <a href="{{ route('dashboard.submissions.audiobook.chapters', $item) }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold rounded-xl border border-amber-200 transition">
                                    <x-app-icon name="music" class="h-4 w-4 text-amber-700" />
                                    <span>अध्याय व ट्रैक जोड़ें / देखें</span>
                                </a>
                            @endif

                            <a href="{{ route('dashboard', ['tab' => 'submissions']) }}" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 transition">
                                <span>मेरी सभी प्रस्तुतियां देखें</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>