@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'सार्वजनिक डोमेन' : 'Public Domain', 'url' => null],
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
            <div class="border-b border-slate-200 pb-6 text-center sm:text-left">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    {{ $locale === 'hi' ? 'सांस्कृतिक विरासत नीति' : 'Cultural Heritage Statement' }}
                </span>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    {{ $locale === 'hi' ? 'सार्वजनिक डोमेन वक्तव्य (Public Domain Statement)' : 'Public Domain Statement' }}
                </h1>
                <p class="mt-2 text-xs sm:text-sm text-slate-500">
                    {{ $locale === 'hi' ? 'अंतिम अद्यतन: ' . date('F Y') : 'Last Updated: ' . date('F Y') }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 space-y-6 text-slate-700 text-sm leading-relaxed">
                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        1. {{ $locale === 'hi' ? 'सार्वजनिक डोमेन क्या है?' : 'What is the Public Domain?' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'सार्वजनिक डोमेन (Public Domain) में वे सभी साहित्यिक, दार्शनिक एवं कलात्मक कृतियाँ शामिल होती हैं जिनका कॉपीराइट समाप्त हो चुका है या जिन पर किसी व्यक्ति अथवा संस्था का अनन्य बौद्धिक संपदा अधिकार नहीं है। ऐसी कृतियों का अध्ययन, वाचन और ज्ञान-प्रसार बिना किसी कानूनी बाधा के हर नागरिक का अधिकार है।'
                            : 'Works in the public domain are creative materials not protected by intellectual property laws such as copyright. Anyone can legally access, read, quote, study, and share public domain literature without requiring prior licensing or royalty payments.'
                        }}
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        2. {{ $locale === 'hi' ? 'भारतीय विधिक मानक (60 वर्ष का नियम)' : 'The 60-Year Posthumous Rule' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'भारतीय कॉपीराइट अधिनियम, 1957 (The Copyright Act, 1957) के प्रावधानों के अनुसार, लेखक के निधन के 60 वर्ष पूर्ण होने के बाद कृति स्वतः सार्वजनिक डोमेन में प्रविष्ट हो जाती है। हमारे पुस्तकालय में मुंशी प्रेमचंद (1936), जयशंकर प्रसाद (1937), शरतचंद्र चट्टोपाध्याय और स्वामी विवेकानंद जैसे दिग्गजों की रचनाएँ इसी विधिक अधिकार के तहत संरक्षित एवं प्रदर्शित हैं।'
                            : 'Under Section 22 of the Indian Copyright Act, 1957, literary copyright expires 60 calendar years following the death of the author. Iconic works by Munshi Premchand (d. 1936), Jaishankar Prasad (d. 1937), Swami Vivekananda (d. 1902), and other historic literary figures are fully within the Indian public domain.'
                        }}
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        3. {{ $locale === 'hi' ? 'डिजिटलीकरण एवं संरक्षण' : 'Preservation & Integrity' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'हम सार्वजनिक डोमेन ग्रंथों के मूल स्वरूप, भाषा और वर्तनी को विकृत किए बिना सटीक रूप से संरक्षित करने के लिए समर्पित हैं। यदि किसी कृति की अवधि या संस्करण को लेकर कोई नवीन विधिक शोध उपलब्ध हो, तो हम उसका सहर्ष स्वागत करते हैं।'
                            : 'Our preservation pipeline aims to preserve the authenticity of original Hindi texts, historical typography, and original prefaces while delivering accessible digital PDF files for scholarship and reading.'
                        }}
                    </p>
                </section>
            </div>
        </div>
    </x-ui.container>
</div>
@endsection
