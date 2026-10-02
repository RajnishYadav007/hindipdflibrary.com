@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $isHindi = $locale === 'hi';

    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $isHindi ? 'वेद एवं पुराण' : 'Vedas & Puranas', 'url' => null],
    ];

    $puranasDirectory = [
        ['name_hi' => 'विष्णु पुराण', 'name_en' => 'Vishnu Purana', 'deity' => 'भगवान विष्णु (सात्विक)', 'shlokas' => '२३,०००', 'slug' => 'vishnu-puran'],
        ['name_hi' => 'श्रीमद्भागवत महापुराण', 'name_en' => 'Shrimad Bhagavata Purana', 'deity' => 'श्री कृष्ण / विष्णु (सात्विक)', 'shlokas' => '१८,०००', 'slug' => 'bhagwat-puran'],
        ['name_hi' => 'नारद पुराण', 'name_en' => 'Narada Purana', 'deity' => 'देवर्षि नारद (सात्विक)', 'shlokas' => '२५,०००', 'slug' => 'narada-puran'],
        ['name_hi' => 'गरुड़ पुराण', 'name_en' => 'Garuda Purana', 'deity' => 'गरुड़ / विष्णु (सात्विक)', 'shlokas' => '१९,०००', 'slug' => 'garuda-puran'],
        ['name_hi' => 'पद्म पुराण', 'name_en' => 'Padma Purana', 'deity' => 'भगवान विष्णु (सात्विक)', 'shlokas' => '५५,०००', 'slug' => 'padma-puran'],
        ['name_hi' => 'वराह पुराण', 'name_en' => 'Varaha Purana', 'deity' => 'वराह अवतार (सात्विक)', 'shlokas' => '२४,०००', 'slug' => 'varaha-puran'],
        ['name_hi' => 'शिव महापुराण', 'name_en' => 'Shiva Purana', 'deity' => 'भगवान शिव (तामसिक/शैव)', 'shlokas' => '२४,०००', 'slug' => 'shiva-puran'],
        ['name_hi' => 'लिंग पुराण', 'name_en' => 'Linga Purana', 'deity' => 'शिव लिंग स्वरूप (शैव)', 'shlokas' => '११,०००', 'slug' => 'linga-puran'],
        ['name_hi' => 'स्कन्द पुराण', 'name_en' => 'Skanda Purana', 'deity' => 'भगवान कार्तिकेय/शिव (शैव)', 'shlokas' => '८१,१००', 'slug' => 'skanda-puran'],
        ['name_hi' => 'अग्नि पुराण', 'name_en' => 'Agni Purana', 'deity' => 'अग्नि देव / ज्ञान (राजसिक)', 'shlokas' => '१५,४००', 'slug' => 'agni-puran'],
        ['name_hi' => 'कूर्म पुराण', 'name_en' => 'Kurma Purana', 'deity' => 'कूर्म अवतार (तामसिक)', 'shlokas' => '१७,०००', 'slug' => 'kurma-puran'],
        ['name_hi' => 'मत्स्य पुराण', 'name_en' => 'Matsya Purana', 'deity' => 'मत्स्य अवतार (तामसिक)', 'shlokas' => '१४,०००', 'slug' => 'matsya-puran'],
        ['name_hi' => 'भविष्य पुराण', 'name_en' => 'Bhavishya Purana', 'deity' => 'सूर्य देव / भविष्य दर्शन (राजसिक)', 'shlokas' => '१४,५००', 'slug' => 'bhavishya-puran'],
        ['name_hi' => 'ब्रह्म पुराण', 'name_en' => 'Brahma Purana', 'deity' => 'ब्रह्मा जी (आदि पुराण)', 'shlokas' => '१०,०००', 'slug' => 'brahma-puran'],
        ['name_hi' => 'ब्रह्माण्ड पुराण', 'name_en' => 'Brahmanda Purana', 'deity' => 'सृष्टि व ललिता सहस्रनाम', 'shlokas' => '१२,०००', 'slug' => 'brahmanda-puran'],
        ['name_hi' => 'ब्रह्मवैवर्त पुराण', 'name_en' => 'Brahmavaivarta Purana', 'deity' => 'राधा-कृष्ण लीला', 'shlokas' => '१८,०००', 'slug' => 'brahmavaivarta-puran'],
        ['name_hi' => 'मार्कण्डेय पुराण', 'name_en' => 'Markandeya Purana', 'deity' => 'दुर्गा सप्तशती / देवी माहात्म्य', 'shlokas' => '९,०००', 'slug' => 'markandeya-puran'],
        ['name_hi' => 'वामन पुराण', 'name_en' => 'Vamana Purana', 'deity' => 'वामन अवतार', 'shlokas' => '१०,०००', 'slug' => 'vamana-puran'],
    ];
@endphp

@section('content')
<div class="space-y-12 pb-16">
    <!-- Breadcrumbs -->
    <div class="border-b border-slate-200/80 bg-slate-50/50 py-3">
        <x-ui.container size="default">
            <x-breadcrumb :items="$breadcrumbs" />
        </x-ui.container>
    </div>

    <!-- Vedic Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-amber-500/10 via-warm-50 to-white py-12 sm:py-16 border-b border-warm-200/70">
        <div class="absolute inset-0 bg-[radial-gradient(#f59e0b_1px,transparent_1px)] [background-size:20px_20px] opacity-15"></div>
        <x-ui.container size="default" class="relative">
            <div class="max-w-4xl mx-auto text-center space-y-5">
                <div class="inline-flex items-center gap-2 rounded-full bg-amber-100/90 border border-amber-300 px-4 py-1 text-xs font-bold text-amber-950 shadow-2xs">
                    <span>🕉️ सनातन ज्ञान धरोहर</span>
                    <span class="h-1 w-1 rounded-full bg-amber-600"></span>
                    <span>All 4 Vedas &amp; 18 Mahapuranas</span>
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold text-ink-900 font-serif tracking-tight leading-tight">
                    Download All Ved and Puran PDF Hindi Free — चारों वेद एवं १८ महापुराण
                </h1>

                <p class="text-sm sm:text-base text-slate-700 leading-relaxed max-w-2xl mx-auto">
                    सम्पूर्ण ४ पवित्र वेद (ऋग्वेद, यजुर्वेद, सामवेद, अथर्ववेद) और १८ महापुराण (गीताप्रेस गोरखपुर संस्करण), प्रसिद्ध चालीसा व स्तोत्र संग्रह का शुद्ध देवनागरी पाठ, अर्थ, ऑडियो वाचन एवं निःशुल्क पीडीएफ डाउनलोड।
                </p>

                <!-- Search Topic Keywords Pills -->
                <div class="pt-2 flex flex-wrap items-center justify-center gap-2 text-xs">
                    <span class="text-slate-400 font-medium">लोकप्रिय खोजें:</span>
                    <a href="{{ route('search', ['q' => 'वेद पुराण']) }}" class="rounded-lg bg-amber-100/70 hover:bg-amber-200/80 px-2.5 py-1 font-semibold text-amber-950 border border-amber-200">#ved puran pdf</a>
                    <a href="{{ route('search', ['q' => '18 पुराण']) }}" class="rounded-lg bg-amber-100/70 hover:bg-amber-200/80 px-2.5 py-1 font-semibold text-amber-950 border border-amber-200">#all 18 puranas in hindi</a>
                    <a href="{{ route('search', ['q' => 'चार वेद']) }}" class="rounded-lg bg-amber-100/70 hover:bg-amber-200/80 px-2.5 py-1 font-semibold text-amber-950 border border-amber-200">#4 vedas pdf</a>
                    <a href="{{ route('search', ['q' => 'हनुमान चालीसा']) }}" class="rounded-lg bg-amber-100/70 hover:bg-amber-200/80 px-2.5 py-1 font-semibold text-amber-950 border border-amber-200">#hanuman chalisa lyrics</a>
                    <a href="{{ route('search', ['q' => 'शिव पुराण']) }}" class="rounded-lg bg-amber-100/70 hover:bg-amber-200/80 px-2.5 py-1 font-semibold text-amber-950 border border-amber-200">#shiv puran pdf</a>
                    <a href="{{ route('search', ['q' => 'गीताप्रेस']) }}" class="rounded-lg bg-amber-100/70 hover:bg-amber-200/80 px-2.5 py-1 font-semibold text-amber-950 border border-amber-200">#ved puran gita press</a>
                </div>

                <!-- Quick Highlights Counter Badges -->
                <div class="pt-4 grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-2xl mx-auto text-center">
                    <div class="rounded-2xl border border-warm-300 bg-white/80 p-3 shadow-2xs">
                        <span class="block text-xl font-bold text-ink-900 font-serif">४ वेद</span>
                        <span class="text-[11px] text-slate-500 font-medium">The 4 Vedas</span>
                    </div>
                    <div class="rounded-2xl border border-warm-300 bg-white/80 p-3 shadow-2xs">
                        <span class="block text-xl font-bold text-ink-900 font-serif">१८ महापुराण</span>
                        <span class="text-[11px] text-slate-500 font-medium">18 Mahapuranas</span>
                    </div>
                    <div class="rounded-2xl border border-warm-300 bg-white/80 p-3 shadow-2xs">
                        <span class="block text-xl font-bold text-amber-700 font-serif">चालीसा व पाठ</span>
                        <span class="text-[11px] text-slate-500 font-medium">Lyrics &amp; Audio</span>
                    </div>
                    <div class="rounded-2xl border border-warm-300 bg-white/80 p-3 shadow-2xs">
                        <span class="block text-xl font-bold text-emerald-700 font-serif">१००% निःशुल्क</span>
                        <span class="text-[11px] text-slate-500 font-medium">Open Access PDF</span>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>

    <!-- Main Navigation Hub with Interactive Tabs -->
    <section x-data="{ activeTab: 'all' }">
        <x-ui.container size="default">
            <!-- Section Selector Tabs -->
            <div class="flex flex-wrap items-center justify-center gap-2 mb-10">
                <button 
                    type="button" 
                    @click="activeTab = 'all'" 
                    :class="activeTab === 'all' ? 'bg-ink-900 text-white shadow-sm' : 'bg-white border border-warm-300 text-slate-700 hover:bg-warm-100'"
                    class="rounded-xl px-5 py-2.5 text-xs sm:text-sm font-bold transition cursor-pointer"
                >
                    सभी पवित्र ग्रंथ (All Scriptures)
                </button>
                <button 
                    type="button" 
                    @click="activeTab = 'vedas'" 
                    :class="activeTab === 'vedas' ? 'bg-ink-900 text-white shadow-sm' : 'bg-white border border-warm-300 text-slate-700 hover:bg-warm-100'"
                    class="rounded-xl px-5 py-2.5 text-xs sm:text-sm font-bold transition cursor-pointer"
                >
                    चारों वेद (The 4 Vedas)
                </button>
                <button 
                    type="button" 
                    @click="activeTab = 'puranas'" 
                    :class="activeTab === 'puranas' ? 'bg-ink-900 text-white shadow-sm' : 'bg-white border border-warm-300 text-slate-700 hover:bg-warm-100'"
                    class="rounded-xl px-5 py-2.5 text-xs sm:text-sm font-bold transition cursor-pointer"
                >
                    १८ महापुराण (18 Mahapuranas)
                </button>
                <button 
                    type="button" 
                    @click="activeTab = 'chalisas'" 
                    :class="activeTab === 'chalisas' ? 'bg-ink-900 text-white shadow-sm' : 'bg-white border border-warm-300 text-slate-700 hover:bg-warm-100'"
                    class="rounded-xl px-5 py-2.5 text-xs sm:text-sm font-bold transition cursor-pointer"
                >
                    चालीसा, स्तोत्र व आरती (Chalisas &amp; Lyrics)
                </button>
            </div>

            <!-- Published Books Grid -->
            @if ($books->isNotEmpty())
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 sm:gap-6">
                    @foreach ($books as $book)
                        @php
                            $isVeda = $vedas->contains($book);
                            $isPurana = $puranas->contains($book);
                            $isChalisa = $chalisas->contains($book);
                        @endphp
                        <div 
                            x-show="activeTab === 'all' || (activeTab === 'vedas' && {{ $isVeda ? 'true' : 'false' }}) || (activeTab === 'puranas' && {{ $isPurana ? 'true' : 'false' }}) || (activeTab === 'chalisas' && {{ $isChalisa ? 'true' : 'false' }})\"
                            x-transition
                        >
                            <x-book-card :book="$book" />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-warm-300 bg-white p-12 text-center shadow-2xs max-w-lg mx-auto">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-amber-800 text-2xl">
                        🕉️
                    </div>
                    <h3 class="text-lg font-bold text-ink-900 font-serif">ग्रंथ संग्रह तैयार हो रहा है</h3>
                    <p class="mt-2 text-xs text-slate-600">
                        शीघ्र ही सभी 18 पुराण और 4 वेद ऑनलाइन अध्ययन व पीडीएफ हेतु उपलब्ध होंगे।
                    </p>
                </div>
            @endif
        </x-ui.container>
    </section>

    <!-- Complete 18 Mahapuranas Definitive Reference Directory Table -->
    <section class="border-t border-warm-200/80 pt-12 bg-white/60 py-12">
        <x-ui.container size="default">
            <div class="mb-8 text-center max-w-3xl mx-auto">
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900 uppercase tracking-wider">
                    प्रामाणिक सूची • Definitive Directory
                </span>
                <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold text-ink-900 font-serif">
                    अठारह महापुराण विवरणी (18 Mahapuranas Overview)
                </h2>
                <p class="mt-2 text-xs sm:text-sm text-slate-600">
                    महर्षि वेदव्यास द्वारा रचित अठारह महापुराणों का नाम, प्रधान देवता एवं श्लोक संख्या विवरण:
                </p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-warm-300 bg-white shadow-2xs">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-warm-200 text-left text-xs sm:text-sm">
                        <thead class="bg-warm-100/70 text-ink-900 font-semibold">
                            <tr>
                                <th scope="col" class="px-4 py-3.5">क्र.सं. (No.)</th>
                                <th scope="col" class="px-4 py-3.5">महापुराण का नाम (Hindi)</th>
                                <th scope="col" class="px-4 py-3.5">English Title</th>
                                <th scope="col" class="px-4 py-3.5">प्रधान देवता / प्रकृति</th>
                                <th scope="col" class="px-4 py-3.5">श्लोक संख्या</th>
                                <th scope="col" class="px-4 py-3.5 text-right">अध्ययन (Action)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-warm-200 text-slate-700">
                            @foreach ($puranasDirectory as $idx => $pItem)
                                <tr class="hover:bg-amber-50/40 transition">
                                    <td class="px-4 py-3 font-mono font-medium text-slate-500">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3 font-bold text-ink-900 font-serif">{{ $pItem['name_hi'] }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $pItem['name_en'] }}</td>
                                    <td class="px-4 py-3 text-xs font-medium text-amber-950">
                                        <span class="rounded-md bg-amber-100/80 px-2 py-0.5 border border-amber-200">
                                            {{ $pItem['deity'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-slate-600">{{ $pItem['shlokas'] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('search', ['q' => $pItem['name_hi']]) }}" class="inline-flex items-center gap-1 text-xs font-bold text-terracotta-600 hover:text-terracotta-800 hover:underline">
                                            <span>खोजें व पढ़ें</span>
                                            <span>&rarr;</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-ui.container>
    </section>

    <!-- The 4 Vedas Architectural Overview -->
    <section class="border-t border-warm-200/80 pt-10">
        <x-ui.container size="default">
            <div class="mb-8 text-center max-w-3xl mx-auto">
                <span class="rounded-full bg-warm-200 px-3 py-1 text-xs font-bold text-ink-900 uppercase tracking-wider">
                    चतुर्वेद परिचय • The 4 Vedas
                </span>
                <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold text-ink-900 font-serif">
                    चारों वेदों का संक्षिप्त परिचय एवं विषय वस्तु
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="rounded-2xl border border-warm-300 bg-white p-6 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-ink-900 font-serif">१. ऋग्वेद (Rigveda)</h3>
                        <span class="rounded-full bg-amber-100 text-amber-900 px-2.5 py-0.5 text-xs font-semibold">१० मण्डल • १०२८ सूक्त</span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        विश्व का प्राचीनतम ग्रंथ। इसमें देवताओं की स्तुति, गायत्री मंत्र, दार्शनिक ऋचाएं और सृष्टि उत्पत्ति का नासदीय सूक्त संगृहीत है।
                    </p>
                    <a href="{{ route('search', ['q' => 'ऋग्वेद']) }}" class="inline-flex items-center text-xs font-bold text-ink-900 hover:text-terracotta-600 pt-2">
                        ऋग्वेद संहिता खोजें &rarr;
                    </a>
                </div>

                <div class="rounded-2xl border border-warm-300 bg-white p-6 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-ink-900 font-serif">२. यजुर्वेद (Yajurveda)</h3>
                        <span class="rounded-full bg-warm-200 text-ink-900 px-2.5 py-0.5 text-xs font-semibold">शुक्ल व कृष्ण यजुर्वेद</span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        यज्ञ, कर्मकांड और आध्यात्मिक साधना की विधि का ग्रंथ। ईशावास्योपनिषद शुक्ल यजुर्वेद का ही ४०वाँ अध्याय है।
                    </p>
                    <a href="{{ route('search', ['q' => 'यजुर्वेद']) }}" class="inline-flex items-center text-xs font-bold text-ink-900 hover:text-terracotta-600 pt-2">
                        यजुर्वेद संहिता खोजें &rarr;
                    </a>
                </div>

                <div class="rounded-2xl border border-warm-300 bg-white p-6 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-ink-900 font-serif">३. सामवेद (Samaveda)</h3>
                        <span class="rounded-full bg-sky-100 text-sky-900 px-2.5 py-0.5 text-xs font-semibold">भारतीय संगीत का मूल</span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        गेय ऋचाओं और संगीत का अनुपम कोष। भगवान श्रीकृष्ण ने गीता में स्वयं को वेदों में सामवेद कहा है (वेदानां सामवेदोऽस्मि)।
                    </p>
                    <a href="{{ route('search', ['q' => 'सामवेद']) }}" class="inline-flex items-center text-xs font-bold text-ink-900 hover:text-terracotta-600 pt-2">
                        सामवेद संहिता खोजें &rarr;
                    </a>
                </div>

                <div class="rounded-2xl border border-warm-300 bg-white p-6 shadow-2xs space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-ink-900 font-serif">४. अथर्ववेद (Atharvaveda)</h3>
                        <span class="rounded-full bg-emerald-100 text-emerald-900 px-2.5 py-0.5 text-xs font-semibold">२० काण्ड • ५९७७ मन्त्र</span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        आयुर्वेद, भौतिक विज्ञान, राष्ट्र-प्रेम (भूमि सूक्त), सामाजिक कर्तव्य एवं आध्यात्मिक रहस्यों का विशाल भंडार।
                    </p>
                    <a href="{{ route('search', ['q' => 'अथर्ववेद']) }}" class="inline-flex items-center text-xs font-bold text-ink-900 hover:text-terracotta-600 pt-2">
                        अथर्ववेद संहिता खोजें &rarr;
                    </a>
                </div>
            </div>
        </x-ui.container>
    </section>

    <!-- SEO FAQ Section targeting search terms -->
    <section class="border-t border-warm-200/80 pt-12">
        <x-ui.container size="default">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-ink-900 font-serif">
                        अक्सर पूछे जाने वाले प्रश्न (Frequently Asked Questions)
                    </h2>
                    <p class="mt-1 text-xs text-slate-500">
                        वेद, पुराण, चालीसा पाठ एवं पीडीएफ डाउनलोड से जुड़े सामान्य प्रश्न
                    </p>
                </div>

                <div class="space-y-3" x-data="{ active: 1 }">
                    <div class="rounded-xl border border-warm-300 bg-white p-4 shadow-2xs">
                        <button 
                            @click="active = (active === 1 ? null : 1)" 
                            class="flex w-full items-center justify-between text-left text-sm font-semibold text-ink-900 cursor-pointer"
                        >
                            <span>चारों वेदों के नाम क्या हैं और इनका क्या महत्व है?</span>
                            <span class="text-slate-400 font-bold" x-text="active === 1 ? '−' : '+'"></span>
                        </button>
                        <div x-show="active === 1" x-collapse class="mt-2 text-xs text-slate-600 leading-relaxed">
                            सनातन धर्म में चार मुख्य वेद हैं: ऋग्वेद (ज्ञान), यजुर्वेद (कर्म), सामवेद (भक्ति व संगीत), और अथर्ववेद (विज्ञान व समाज)। ये मानव सभ्यता के प्राचीनतम ग्रंथ माने जाते हैं।
                        </div>
                    </div>

                    <div class="rounded-xl border border-warm-300 bg-white p-4 shadow-2xs">
                        <button 
                            @click="active = (active === 2 ? null : 2)" 
                            class="flex w-full items-center justify-between text-left text-sm font-semibold text-ink-900 cursor-pointer"
                        >
                            <span>अठारह महापुराण कौन से हैं और इनके रचयिता कौन हैं?</span>
                            <span class="text-slate-400 font-bold" x-text="active === 2 ? '−' : '+'"></span>
                        </button>
                        <div x-show="active === 2" x-collapse class="mt-2 text-xs text-slate-600 leading-relaxed">
                            महर्षि कृष्ण द्वैपायन वेदव्यास जी ने 18 महापुराणों की रचना की: ब्रह्म, पद्म, विष्णु, शिव, भागवत, नारद, मार्कण्डेय, अग्नि, भविष्य, ब्रह्मवैवर्त, लिंग, वराह, स्कन्द, वामन, कूर्म, मत्स्य, गरुड़ और ब्रह्माण्ड पुराण।
                        </div>
                    </div>

                    <div class="rounded-xl border border-warm-300 bg-white p-4 shadow-2xs">
                        <button 
                            @click="active = (active === 3 ? null : 3)" 
                            class="flex w-full items-center justify-between text-left text-sm font-semibold text-ink-900 cursor-pointer"
                        >
                            <span>क्या यहाँ से हनुमान चालीसा और शिव चालीसा का पाठ व ऑडियो सुन सकते हैं?</span>
                            <span class="text-slate-400 font-bold" x-text="active === 3 ? '−' : '+'"></span>
                        </button>
                        <div x-show="active === 3" x-collapse class="mt-2 text-xs text-slate-600 leading-relaxed">
                            हाँ, हिन्दी पीडीएफ पुस्तकालय पर आप हनुमान चालीसा, शिव चालीसा आदि के सम्पूर्ण दोहे और चौपाइयों का पाठ (Lyrics) पढ़ने के साथ-साथ शुद्ध हिन्दी में ऑडियो वाचन भी सुन सकते हैं और पीडीएफ भी डाउनलोड कर सकते हैं।
                        </div>
                    </div>

                    <div class="rounded-xl border border-warm-300 bg-white p-4 shadow-2xs">
                        <button 
                            @click="active = (active === 4 ? null : 4)" 
                            class="flex w-full items-center justify-between text-left text-sm font-semibold text-ink-900 cursor-pointer"
                        >
                            <span>क्या वेद और पुराणों की पीडीएफ डाउनलोड पूर्णतः निःशुल्क है?</span>
                            <span class="text-slate-400 font-bold" x-text="active === 4 ? '−' : '+'"></span>
                        </button>
                        <div x-show="active === 4" x-collapse class="mt-2 text-xs text-slate-600 leading-relaxed">
                            हाँ, सभी धार्मिक एवं ऐतिहासिक कृतियां सार्वजनिक अध्ययन व शोध हेतु पूर्णतः निःशुल्क उपलब्ध हैं।
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>
</div>
@endsection