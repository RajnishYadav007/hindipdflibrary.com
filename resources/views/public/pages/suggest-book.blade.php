@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'पुस्तक सुझाव' : 'Suggest a Book', 'url' => null],
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
                <span class="text-xs font-bold uppercase tracking-wider text-brand-700">
                    {{ $locale === 'hi' ? 'सामुदायिक सहभागिता' : 'Community Contribution' }}
                </span>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    {{ $locale === 'hi' ? 'पुस्तक का सुझाव दें' : 'Suggest a Book for Digitization' }}
                </h1>
                <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                    {{ $locale === 'hi' 
                        ? 'यदि आप हिन्दी का कोई प्रसिद्ध उपन्यास, कहानी संग्रह, काव्य या विचार-ग्रन्थ हमारी डिजिटल लाइब्रेरी में देखना चाहते हैं, तो कृपया नीचे विवरण दर्ज करें।' 
                        : 'Help us expand our Hindi literary repository. Submit details of rare works, classic novels, poetry, or historical publications you wish to see archived.' 
                    }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-2xs">
                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-semibold text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('pages.suggest-book.submit') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                {{ $locale === 'hi' ? 'पुस्तक का शीर्षक (Title)' : 'Book Title' }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="title" 
                                id="title" 
                                value="{{ old('title') }}"
                                required 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20"
                                placeholder="{{ $locale === 'hi' ? 'उदा. राग दरबारी' : 'e.g. Raag Darbari' }}"
                            />
                            @error('title')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="author" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                {{ $locale === 'hi' ? 'लेखक का नाम (Author)' : 'Author Name' }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="author" 
                                id="author" 
                                value="{{ old('author') }}"
                                required 
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20"
                                placeholder="{{ $locale === 'hi' ? 'उदा. श्रीलाल शुक्ल' : 'e.g. Shrilal Shukla' }}"
                            />
                            @error('author')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="publication_year" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                {{ $locale === 'hi' ? 'प्रकाशन वर्ष (यदि ज्ञात हो)' : 'Publication Year (If known)' }}
                            </label>
                            <input 
                                type="number" 
                                name="publication_year" 
                                id="publication_year" 
                                value="{{ old('publication_year') }}"
                                min="1800"
                                max="{{ date('Y') }}"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20"
                                placeholder="{{ $locale === 'hi' ? 'उदा. 1968' : 'e.g. 1968' }}"
                            />
                            @error('publication_year')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                {{ $locale === 'hi' ? 'आपका ईमेल (वैकल्पिक)' : 'Your Email (Optional)' }}
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="{{ old('email') }}"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20"
                                placeholder="yourname@example.com"
                            />
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            {{ $locale === 'hi' ? 'अतिरिक्त जानकारी / स्रोत लिंक' : 'Additional Notes / Source Link' }}
                        </label>
                        <textarea 
                            name="notes" 
                            id="notes" 
                            rows="3" 
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20"
                            placeholder="{{ $locale === 'hi' ? 'पुस्तक के विषय में कोई विशेष जानकारी या ऑनलाइन स्रोत लिंक...' : 'Any details about the edition, public domain status, or archive source...' }}"
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full sm:w-auto rounded-xl bg-brand-700 px-6 py-2.5 text-xs font-bold text-white hover:bg-brand-800 shadow-xs transition"
                        >
                            {{ $locale === 'hi' ? 'सुझाव सबमिट करें' : 'Submit Suggestion' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-ui.container>
</div>
@endsection
