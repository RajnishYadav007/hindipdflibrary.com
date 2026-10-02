@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'कॉपीराइट एवं डीएमसीए' : 'Copyright & DMCA', 'url' => null],
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
                    {{ $locale === 'hi' ? 'बौद्धिक संपदा नीति' : 'Intellectual Property Policy' }}
                </span>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    {{ $locale === 'hi' ? 'कॉपीराइट नीति एवं डीएमसीए (Copyright & DMCA)' : 'Copyright Policy & DMCA Notice' }}
                </h1>
                <p class="mt-2 text-xs sm:text-sm text-slate-500">
                    {{ $locale === 'hi' ? 'अंतिम अद्यतन: ' . date('F Y') : 'Last Updated: ' . date('F Y') }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 space-y-6 text-slate-700 text-sm leading-relaxed">
                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        1. {{ $locale === 'hi' ? 'बौद्धिक संपदा के प्रति प्रतिबद्धता' : 'Commitment to Intellectual Property Rights' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'हिन्दी पीडीएफ पुस्तकालय (Hindi PDF Library) लेखकों, प्रकाशकों और अधिकार धारकों के कॉपीराइट अधिकारों का पूर्ण सम्मान करता है। हमारा मुख्य उद्देश्य केवल उन ऐतिहासिक व सांस्कृतिक ग्रंथों का संरक्षण और प्रसार है जो विधिक रूप से सार्वजनिक डोमेन (Public Domain) में आ चुके हैं अथवा जिनके वितरण की अनुमति प्राप्त है।'
                            : 'Hindi PDF Library respects the intellectual property rights of authors, publishers, and legal copyright holders. Our repository indexes works that have legally entered the public domain, are distributed under creative open licenses, or possess verified permissions.'
                        }}
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        2. {{ $locale === 'hi' ? 'भारतीय कॉपीराइट अधिनियम, 1957' : 'Indian Copyright Act, 1957' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'भारतीय कॉपीराइट अधिनियम, 1957 की धारा 22 के अनुसार, साहित्यिक कृतियों का कॉपीराइट लेखक के निधन के वर्ष के बाद 60 वर्षों तक मान्य रहता है। इसके उपरांत वह रचना सार्वजनिक डोमेन में आ जाती है और उसे जनहित में संरक्षित एवं साझा किया जा सकता है।'
                            : 'In accordance with Section 22 of the Indian Copyright Act, 1957, copyright in published literary works subsists until 60 years from the beginning of the calendar year next following the year in which the author dies. Thereafter, the work enters the public domain.'
                        }}
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        3. {{ $locale === 'hi' ? 'कॉपीराइट आपत्ति एवं सामग्री हटाने की प्रक्रिया (Takedown Notice)' : 'DMCA & Takedown Notice Procedure' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'यदि आप किसी पुस्तक या रचना के वैध कॉपीराइट धारक हैं और आपको लगता है कि कोई सामग्री अनजाने में बिना वैध अनुमति के प्रदर्शित की गई है, तो आप हमें सीधे नोटिस भेज सकते हैं। हम नोटिस प्राप्त होने के 48 घंटों के भीतर सामग्री की समीक्षा कर उसे हटाने की त्वरित कार्यवाही करते हैं।'
                            : 'If you are a legitimate copyright owner or authorized agent and believe that content indexed on this platform infringes your copyright, please provide a written notice containing:'
                        }}
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-xs text-slate-600 pl-2">
                        <li>{{ $locale === 'hi' ? 'रचना का सटीक शीर्षक एवं वेबसाइट पर उसका URL' : 'Exact title of the work and the specific URL on our website.' }}</li>
                        <li>{{ $locale === 'hi' ? 'कॉपीराइट स्वामित्व या अधिकृत एजेंट होने का प्रमाण' : 'Evidence demonstrating legal copyright ownership or authorized agency.' }}</li>
                        <li>{{ $locale === 'hi' ? 'आपका संपर्क विवरण (नाम, पता, ईमेल, फोन नंबर)' : 'Your contact information (name, address, email, telephone number).' }}</li>
                        <li>{{ $locale === 'hi' ? 'एक सद्भावनापूर्ण वक्तव्य कि सामग्री का उपयोग अनधिकृत है' : 'A statement of good faith belief that the disputed use is not authorized.' }}</li>
                    </ul>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        4. {{ $locale === 'hi' ? 'नामित कॉपीराइट अधिकारी' : 'Designated Copyright Agent' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'सभी कॉपीराइट नोटिस सीधे हमारे नामित ईमेल पते पर भेजे जा सकते हैं: '
                            : 'All formal takedown notices should be addressed to our Designated Copyright Representative at:'
                        }}
                        <span class="font-semibold text-brand-700">dmca@hindipdflibrary.com</span>
                    </p>
                </section>
            </div>
        </div>
    </x-ui.container>
</div>
@endsection
