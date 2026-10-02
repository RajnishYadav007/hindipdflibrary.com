@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'गोपनीयता नीति' : 'Privacy Policy', 'url' => null],
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
                    {{ $locale === 'hi' ? 'विधिक एवं सुरक्षा नीतियाँ' : 'Legal & Privacy Policies' }}
                </span>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    {{ $locale === 'hi' ? 'गोपनीयता नीति (Privacy Policy)' : 'Privacy Policy' }}
                </h1>
                <p class="mt-2 text-xs sm:text-sm text-slate-500">
                    {{ $locale === 'hi' ? 'अंतिम अद्यतन: ' . date('F Y') : 'Last Updated: ' . date('F Y') }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 space-y-6 text-slate-700 text-sm leading-relaxed">
                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        1. {{ $locale === 'hi' ? 'परिचय एवं प्रतिबद्धता' : 'Introduction & Commitment' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'हिन्दी पीडीएफ पुस्तकालय (Hindi PDF Library) हमारे पाठकों की व्यक्तिगत गोपनीयता का सम्मान करता है। हम एक गैर-व्यावसायिक सांस्कृतिक पहल हैं और हम किसी भी तीसरे पक्ष के साथ आपका व्यक्तिगत डेटा साझा, विक्रय या व्यापार नहीं करते हैं।'
                            : 'Hindi PDF Library respects your digital privacy. As a non-commercial cultural project, we do not sell, rent, or trade your personal data with third-party advertisers or data brokers.'
                        }}
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        2. {{ $locale === 'hi' ? 'संग्रहीत जानकारी' : 'Information We Collect' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'जब आप हमारी वेबसाइट पर आते हैं, तो हम केवल आवश्यक तकनीकी डेटा एकत्र करते हैं जैसे कि आईपी एड्रेस (एनालिटिक्स एवं सुरक्षा ऑडिट के लिए हैश किया गया), ब्राउज़र का प्रकार, भाषा प्राथमिकता और देखे गए पृष्ठ। यदि आप खाता बनाते हैं या संपर्क करते हैं, तो केवल आपका नाम और ईमेल सुरक्षित रूप से रखा जाता है।'
                            : 'When visiting our platform, we only process basic technical metadata (hashed IP addresses for DDoS protection, browser type, preferred language, and requested pages). If you register an account or submit feedback, your name and email address are stored with industry-standard encryption.'
                        }}
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        3. {{ $locale === 'hi' ? 'कुकीज़ का उपयोग' : 'Cookie Usage' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'हम केवल सत्र (Session) प्रबंधन और भाषा चयन (हिन्दी/अंग्रेजी) को याद रखने के लिए आवश्यक कुकीज़ का उपयोग करते हैं। हम किसी भी आक्रामक ट्रैकिंग या विज्ञापन कुकीज़ का प्रयोग नहीं करते हैं।'
                            : 'We strictly employ functional session cookies necessary for user authentication and preserving your language preference (Hindi/English). No invasive cross-site advertising trackers are installed.'
                        }}
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        4. {{ $locale === 'hi' ? 'डेटा सुरक्षा एवं संपर्क' : 'Data Security & Inquiries' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'हम आपके डेटा को सुरक्षित रखने के लिए आधुनिक एसएसएल (SSL) एन्क्रिप्शन का उपयोग करते हैं। यदि आपके पास हमारी गोपनीयता नीति के संबंध में कोई प्रश्न है, तो कृपया contact@hindipdflibrary.com पर संपर्क करें।'
                            : 'All web traffic is transmitted via secure TLS/SSL encrypted channels. If you have questions regarding our privacy practices or wish to request data deletion, contact us at contact@hindipdflibrary.com.'
                        }}
                    </p>
                </section>
            </div>
        </div>
    </x-ui.container>
</div>
@endsection
