@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'सेवा शर्तें' : 'Terms of Service', 'url' => null],
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
                    {{ $locale === 'hi' ? 'विधिक सेवा नियम' : 'Legal Terms' }}
                </span>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    {{ $locale === 'hi' ? 'सेवा की शर्तें (Terms of Service)' : 'Terms of Service' }}
                </h1>
                <p class="mt-2 text-xs sm:text-sm text-slate-500">
                    {{ $locale === 'hi' ? 'अंतिम अद्यतन: ' . date('F Y') : 'Last Updated: ' . date('F Y') }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 space-y-6 text-slate-700 text-sm leading-relaxed">
                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        1. {{ $locale === 'hi' ? 'सेवा की स्वीकृति' : 'Acceptance of Terms' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'हिन्दी पीडीएफ पुस्तकालय (Hindi PDF Library) की वेबसाइट का उपयोग करके आप इन सेवा शर्तों के पालन के लिए अपनी सहमति व्यक्त करते हैं।'
                            : 'By accessing or using the Hindi PDF Library website and digital downloads, you agree to comply with and be bound by these Terms of Service.'
                        }}
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        2. {{ $locale === 'hi' ? 'अनुमत उपयोग (शैक्षणिक एवं व्यक्तिगत)' : 'Permitted Non-Commercial Use' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'इस पुस्तकालय पर उपलब्ध सभी सामग्री केवल व्यक्तिगत अध्ययन, शोध, समीक्षा और गैर-व्यावसायिक सांस्कृतिक ज्ञान प्रसार के उद्देश्य से प्रदान की जाती है। वेबसाइट से डाउनलोड की गई किसी भी सामग्री का अनधिकृत व्यावसायिक पुनर्विक्रय निषिद्ध है।'
                            : 'All digital editions are provided strictly for individual reading, educational research, critical commentary, and non-commercial literary preservation. Bulk scraping, automated mass harvesting, or commercial reselling of files is strictly prohibited.'
                        }}
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        3. {{ $locale === 'hi' ? 'उपयोगकर्ता आचरण एवं समीक्षाएं' : 'User Reviews & Contributions' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'उपयोगकर्ताओं द्वारा लिखी गई समीक्षाओं और रेटिंग्स में किसी भी प्रकार की आपत्तिजनक, घृणास्पद या गैर-साहित्यिक भाषा की अनुमति नहीं है। हमारे मॉडरेटर किसी भी नियम विरुद्ध समीक्षा को हटाने का अधिकार रखते हैं।'
                            : 'User-submitted reviews and ratings must remain civil, constructive, and related to literary merit. Moderation staff reserves the right to unpublish submissions containing spam or defamatory language.'
                        }}
                    </p>
                </section>

                <section class="space-y-2">
                    <h2 class="text-base font-bold text-slate-900">
                        4. {{ $locale === 'hi' ? 'दायित्व की सीमा' : 'Limitation of Liability' }}
                    </h2>
                    <p>
                        {{ $locale === 'hi'
                            ? 'पुस्तकालय की सामग्री "जैसी है" (As-Is) के आधार पर उपलब्ध कराई जाती है। हम सामग्री की तकनीकी उपलब्धता या मुद्रण त्रुटियों के संबंध में कोई विधिक गारंटी नहीं देते।'
                            : 'The platform and public domain archives are provided on an "as-is" and "as-available" basis without warranties of uninterrupted uptime or error-free rendering.'
                        }}
                    </p>
                </section>
            </div>
        </div>
    </x-ui.container>
</div>
@endsection
