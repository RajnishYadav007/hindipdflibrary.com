@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'संपर्क (Contact)' : 'Contact Staff', 'url' => null],
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
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
                <!-- Info Column (2 cols) -->
                <div class="md:col-span-2 space-y-6">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-700">
                            {{ $locale === 'hi' ? 'पुस्तकालय सचिवालय' : 'Library Office' }}
                        </span>
                        <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                            {{ $locale === 'hi' ? 'संपर्क करें' : 'Get in Touch' }}
                        </h1>
                        <p class="mt-2 text-sm text-slate-500 leading-relaxed">
                            {{ $locale === 'hi' 
                                ? 'पुस्तकालय की सेवाओं, पुस्तक डिजिटलीकरण, कॉपीराइट प्रश्नों अथवा सामान्य सुझावों के लिए संदेश भेजें।' 
                                : 'Have questions about digital editions, technical issues, or literary contributions? We are here to assist.' 
                            }}
                        </p>
                    </div>

                    <div class="space-y-4 text-xs text-slate-600">
                        <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-3.5 shadow-2xs">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-brand-700 border border-amber-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <div>
                                <span class="block font-bold text-slate-900 mb-0.5">Email Support</span>
                                <span class="text-slate-500">contact@hindipdflibrary.com</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white p-3.5 shadow-2xs">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <span class="block font-bold text-slate-900 mb-0.5">Response Time</span>
                                <span class="text-slate-500">Within 24 to 48 hours (सोमवार - शनिवार)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Column (3 cols) -->
                <div class="md:col-span-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-2xs">
                        @if (session('status'))
                            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-semibold text-emerald-800">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('pages.contact.submit') }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                    {{ $locale === 'hi' ? 'आपका नाम' : 'Full Name' }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="name" 
                                    value="{{ old('name') }}"
                                    required 
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20"
                                    placeholder="{{ $locale === 'hi' ? 'उदा. राहुल शर्मा' : 'e.g. Rahul Sharma' }}"
                                />
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                    {{ $locale === 'hi' ? 'ईमेल पता' : 'Email Address' }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="email" 
                                    value="{{ old('email') }}"
                                    required 
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20"
                                    placeholder="yourname@example.com"
                                />
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="subject" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                    {{ $locale === 'hi' ? 'विषय' : 'Subject' }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="subject" 
                                    id="subject" 
                                    value="{{ old('subject') }}"
                                    required 
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20"
                                    placeholder="{{ $locale === 'hi' ? 'संदेश का संक्षिप्त विषय' : 'Brief subject of your query' }}"
                                />
                                @error('subject')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="message" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                    {{ $locale === 'hi' ? 'संदेश' : 'Message' }} <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    name="message" 
                                    id="message" 
                                    rows="4" 
                                    required 
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600/20"
                                    placeholder="{{ $locale === 'hi' ? 'यहाँ अपना विस्तृत संदेश लिखें...' : 'Write your detailed message here...' }}"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button 
                                type="submit" 
                                class="w-full sm:w-auto rounded-xl bg-brand-700 px-6 py-2.5 text-xs font-bold text-white hover:bg-brand-800 shadow-xs transition"
                            >
                                {{ $locale === 'hi' ? 'संदेश भेजें' : 'Send Message' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.container>
</div>
@endsection
