@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'परिचय (About)' : 'About the Library', 'url' => null],
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
        <div class="max-w-4xl mx-auto space-y-10">
            <!-- Hero Introduction -->
            <div class="border-b border-slate-200 pb-8 text-center sm:text-left">
                <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800 border border-emerald-200 mb-3">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    <span>{{ $locale === 'hi' ? 'सांस्कृतिक एवं शैक्षणिक पहल' : 'Cultural & Educational Initiative' }}</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight">
                    {{ $locale === 'hi' ? 'हिन्दी डिजिटल पुस्तकालय के बारे में' : 'About Hindi PDF Library' }}
                </h1>
                <p class="mt-3 text-base sm:text-lg text-slate-600 leading-relaxed">
                    {{ $locale === 'hi' 
                        ? 'हिन्दी भाषा एवं भारतीय साहित्य की कालजयी कृतियों का डिजिटलीकरण, संरक्षण और निःशुल्क अध्ययन प्रसार।'
                        : 'A dedicated non-commercial digital repository safeguarding and providing open access to classic Hindi literature.' 
                    }}
                </p>
            </div>

            <!-- Core Mission Pillars -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-brand-700 border border-amber-200 mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">
                        {{ $locale === 'hi' ? 'साहित्यिक संरक्षण' : 'Literary Preservation' }}
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        {{ $locale === 'hi' 
                            ? 'दुर्लभ और प्राचीन पाण्डुलिपियों, ऐतिहासिक रचनाओं तथा प्रथम संस्करणों को उच्च गुणवत्ता में डिजिटल प्रारूप में सुरक्षित करना।' 
                            : 'Preserving rare manuscripts, historic Hindi publications, and foundational literary works in high-fidelity digital PDF format.' 
                        }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 12.75v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">
                        {{ $locale === 'hi' ? 'खुला एवं विधिक अभिगम' : 'Open & Legal Access' }}
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        {{ $locale === 'hi' 
                            ? 'सार्वजनिक डोमेन (Public Domain) और अधिकृत लाइसेंस प्राप्त साहित्य को छात्रों, शोधकर्ताओं और पाठकों तक निःशुल्क पहुँचाना।' 
                            : 'Delivering verified public domain literature and authorized cultural works with zero paywalls or commercial restrictions.' 
                        }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-2xs">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">
                        {{ $locale === 'hi' ? 'आधुनिक वाचन अनुभव' : 'Modern Reader Experience' }}
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        {{ $locale === 'hi' 
                            ? 'मोबाइल, टैबलेट और कंप्यूटर पर सहज पढ़ने, सुस्पष्ट देवनागरी फ़ॉन्ट और सुगम डाउनलोडिंग की सुविधा।' 
                            : 'Optimized typography for Devanagari script, responsive online reader, and fast single-click download capabilities.' 
                        }}
                    </p>
                </div>
            </div>

            <!-- Detailed Story & Principles -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 space-y-6 text-slate-700 text-sm leading-relaxed">
                <h2 class="text-xl font-bold text-slate-900">
                    {{ $locale === 'hi' ? 'हमारा उद्देश्य और दृष्टि' : 'Our Mission and Background' }}
                </h2>
                <p>
                    {{ $locale === 'hi' 
                        ? 'हिन्दी पीडीएफ पुस्तकालय (Hindi PDF Library) एक गैर-व्यावसायिक डिजिटल पुस्तकालय परियोजना है। इसका प्राथमिक उद्देश्य हिन्दी भाषा के समृद्ध साहित्य, कालजयी कृतियों, सांस्कृतिक इतिहास तथा शैक्षणिक पुस्तकों को डिजिटल युग में जन-जन तक सुलभ बनाना है।'
                        : 'Hindi PDF Library is a dedicated non-commercial digital preservation project. Our mission is to democratize access to classic Hindi fiction, poetry, historical manuscripts, philosophical essays, and scholarly literature for readers and researchers worldwide.'
                    }}
                </p>
                <p>
                    {{ $locale === 'hi' 
                        ? 'मुंशी प्रेमचंद, जयशंकर प्रसाद, महादेवी वर्मा, सूर्यकांत त्रिपाठी "निराला", हरिवंश राय बच्चन और रामधारी सिंह "दिनकर" जैसे महान लेखकों की रचनाएँ भारतीय सांस्कृतिक धरोहर हैं। हमारा प्रयास है कि इन रचनाओं को आने वाली पीढ़ियों के लिए संरक्षित और सुलभ रखा जाए।'
                        : 'Foundational authors including Munshi Premchand, Jaishankar Prasad, Mahadevi Varma, Nirala, and Harivansh Rai Bachchan shaped Indian literary history. Our platform ensures these masterworks remain universally accessible in modern readable formats.'
                    }}
                </p>
                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center gap-4">
                    <a href="{{ route('books.index') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-700 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-800 transition">
                        {{ $locale === 'hi' ? 'पुस्तकालय संग्रह देखें' : 'Explore Library Books' }}
                    </a>
                    <a href="{{ route('pages.contact') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        {{ $locale === 'hi' ? 'संपर्क करें' : 'Contact Us' }}
                    </a>
                </div>
            </div>
        </div>
    </x-ui.container>
</div>
@endsection
