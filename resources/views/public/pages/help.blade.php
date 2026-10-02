@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'सहायता एवं प्रश्न' : 'Help & FAQs', 'url' => null],
    ];

    $faqs = [
        [
            'q_hi' => 'क्या पुस्तकें पढ़ना और डाउनलोड करना पूर्णतः निःशुल्क है?',
            'q_en' => 'Is reading and downloading books completely free?',
            'a_hi' => 'हाँ, हिन्दी पीडीएफ पुस्तकालय पर उपलब्ध सभी पुस्तकें निःशुल्क पढ़ने एवं पीडीएफ डाउनलोड करने के लिए उपलब्ध हैं। इसके लिए कोई सदस्यता या गुप्त शुल्क नहीं है।',
            'a_en' => 'Yes, all digitized books in Hindi PDF Library are 100% free to read online and download as PDF files. There are no subscriptions or hidden charges.',
        ],
        [
            'q_hi' => 'मैं अपने मोबाइल या कंप्यूटर पर पीडीएफ कैसे डाउनलोड करूँ?',
            'q_en' => 'How do I download a PDF on my phone or computer?',
            'a_hi' => 'किसी भी पुस्तक के पृष्ठ पर जाएं और "डाउनलोड पीडीएफ" (Download PDF) बटन पर क्लिक करें। डाउनलोडिंग तुरंत शुरू हो जाएगी और फ़ाइल आपके डिवाइस के "Downloads" फ़ोल्डर में सुरक्षित हो जाएगी।',
            'a_en' => 'Navigate to any book detail page and click the green "Download PDF" button. Your download will start immediately and save to your device\'s download folder.',
        ],
        [
            'q_hi' => 'क्या पुस्तकें सार्वजनिक डोमेन (Public Domain) में हैं?',
            'q_en' => 'Are these books in the public domain?',
            'a_hi' => 'हम मुख्य रूप से उन पुस्तकों का संग्रह करते हैं जो भारतीय कॉपीराइट अधिनियम (Indian Copyright Act 1957) के अंतर्गत लेखक के निधन के 60 वर्ष पश्चात् सार्वजनिक डोमेन में आ चुकी हैं, अथवा जिनके कॉपीराइट धारक ने खुली पहुँच की अनुमति दी है।',
            'a_en' => 'Our primary collection comprises books that have entered the public domain under Section 22 of the Indian Copyright Act 1957 (60 years posthumous), or works shared under Creative Commons and open cultural licenses.',
        ],
        [
            'q_hi' => 'क्या मैं बिना डाउनलोड किए ऑनलाइन पुस्तक पढ़ सकता हूँ?',
            'q_en' => 'Can I read books directly online without downloading?',
            'a_hi' => 'हाँ, प्रत्येक पुस्तक पृष्ठ पर "ऑनलाइन पढ़ें" (Read Online) का विकल्प उपलब्ध है जो आपको बिना कोई फ़ाइल डाउनलोड किए सीधे ब्राउज़र में पढ़ने की सुविधा प्रदान करता है।',
            'a_en' => 'Yes, every book page features a "Read Online" button that launches our responsive, clean reader right in your browser on desktop, tablet, or smartphone.',
        ],
        [
            'q_hi' => 'क्या मैं कोई दुर्लभ पुस्तक या पाण्डुलिपि का सुझाव दे सकता हूँ?',
            'q_en' => 'Can I suggest or submit a book for your catalog?',
            'a_hi' => 'बिल्कुल! हमारी लाइब्रेरी समुदाय के सहयोग का स्वागत करती है। आप हमारे "पुस्तक का सुझाव दें" (Suggest a Book) पृष्ठ के माध्यम से किसी भी पुस्तक या लेखक की रचना का सुझाव दे सकते हैं।',
            'a_en' => 'Absolutely! We warmly welcome literary suggestions. Please use our "Suggest a Book" page to submit recommendations for authors and titles to be digitized.',
        ],
        [
            'q_hi' => 'यदि किसी पुस्तक के कॉपीराइट को लेकर कोई प्रश्न या आपत्ति हो तो क्या करें?',
            'q_en' => 'What should I do if I hold copyright to a work or have concerns?',
            'a_hi' => 'यदि आप किसी रचना के विधिक अधिकार धारक हैं और आपको लगता है कि सामग्री बिना अनुमति प्रदर्शित है, तो कृपया हमारे "कॉपीराइट एवं डीएमसीए" पृष्ठ या सीधे संपर्क फ़ॉर्म के माध्यम से हमसे संपर्क करें। हम 48 घंटों के भीतर उचित कार्यवाही करते हैं।',
            'a_en' => 'If you are a copyright owner or authorized representative and believe any content has been displayed without appropriate license, please contact our designated staff via our DMCA / Copyright page or Contact form. We respond within 48 hours.',
        ],
    ];
@endphp

@section('content')
<div class="space-y-8 pb-16">
    <!-- Breadcrumb -->
    <div class="border-b border-slate-200/80 bg-slate-50/50 py-3">
        <x-ui.container size="default">
            <x-breadcrumb :items="$breadcrumbs" />
        </x-ui.container>
    </div>

    <!-- Main Content -->
    <x-ui.container size="default">
        <div class="max-w-3xl mx-auto space-y-8">
            <div class="text-center sm:text-left border-b border-slate-200 pb-6">
                <span class="text-xs font-bold uppercase tracking-wider text-brand-700">
                    {{ $locale === 'hi' ? 'पुस्तकालय सहायता केंद्र' : 'Library Help Desk' }}
                </span>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    {{ $locale === 'hi' ? 'अक्सर पूछे जाने वाले प्रश्न (FAQs)' : 'Help & Frequently Asked Questions' }}
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    {{ $locale === 'hi' 
                        ? 'पुस्तकालय के उपयोग, पीडीएफ डाउनलोड, ऑनलाइन वाचन और कॉपीराइट नीतियों से संबंधित सामान्य प्रश्नों के उत्तर।' 
                        : 'Quick answers about reading, downloading, formatting, device compatibility, and library policies.' 
                    }}
                </p>
            </div>

            <!-- FAQs Accordion -->
            <div class="space-y-3.5" x-data="{ activeAccordion: 0 }">
                @foreach ($faqs as $index => $faq)
                    <div class="rounded-xl border border-slate-200 bg-white shadow-2xs overflow-hidden transition">
                        <button 
                            type="button" 
                            @click="activeAccordion = (activeAccordion === {{ $index }} ? null : {{ $index }})"
                            class="flex w-full items-center justify-between gap-4 p-4 text-left font-semibold text-slate-900 hover:text-brand-800 transition text-sm focus-visible:outline-brand-700"
                            :aria-expanded="activeAccordion === {{ $index }}"
                        >
                            <span>{{ $locale === 'hi' ? $faq['q_hi'] : $faq['q_en'] }}</span>
                            <svg 
                                class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200"
                                :class="activeAccordion === {{ $index }} ? 'rotate-180 text-brand-700' : ''"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div 
                            x-show="activeAccordion === {{ $index }}" 
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="border-t border-slate-100 p-4 bg-slate-50/60 text-xs sm:text-sm text-slate-600 leading-relaxed"
                            style="{{ $index === 0 ? '' : 'display: none;' }}"
                        >
                            {{ $locale === 'hi' ? $faq['a_hi'] : $faq['a_en'] }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Help Callout -->
            <div class="rounded-2xl border border-brand-200 bg-brand-50/50 p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ $locale === 'hi' ? 'क्या आपका प्रश्न यहाँ नहीं मिला?' : 'Still have questions?' }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ $locale === 'hi' ? 'हमारी पुस्तकालय टीम से संपर्क करें, हम आपकी सहायता के लिए सदैव तत्पर हैं।' : 'Reach out to our staff team directly and we will assist you.' }}
                    </p>
                </div>
                <a href="{{ route('pages.contact') }}" class="shrink-0 rounded-lg bg-brand-700 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-800 transition">
                    {{ $locale === 'hi' ? 'संपर्क करें' : 'Contact Support' }}
                </a>
            </div>
        </div>
    </x-ui.container>
</div>
@endsection
