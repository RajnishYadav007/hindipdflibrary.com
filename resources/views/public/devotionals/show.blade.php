@extends('layouts.base')

@section('title', "{$item->title} — पाठ, लिरिक्स व ऑडियो (Lyrics & Audio)")

@php
    $locale = app()->getLocale();
    $parentName = $item->type === 'chalisa' 
        ? ($locale === 'hi' ? 'चालीसा संग्रह' : 'Chalisa Sangrah') 
        : ($locale === 'hi' ? 'आरती संग्रह' : 'Aarti Sangrah');
    $parentUrl = $item->type === 'chalisa' ? route('chalisa.index') : route('aarti.index');

    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $parentName, 'url' => $parentUrl],
        ['label' => $item->title, 'url' => null],
    ];
@endphp

@push('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "{{ url('/') }}"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "{{ $parentName }}",
            "item": "{{ $parentUrl }}"
        },
        {
            "@type": "ListItem",
            "position": 3,
            "name": "{{ $item->title }}",
            "item": "{{ url()->current() }}"
        }
    ]
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ $item->title }}",
    "inLanguage": "hi",
    "datePublished": "{{ $item->published_at?->toIso8601String() ?? now()->toIso8601String() }}",
    @if($item->author_name)
    "author": {
        "@type": "Person",
        "name": "{{ $item->author_name }}"
    },
    @endif
    "publisher": {
        "@type": "Organization",
        "name": "{{ config('app.name') }}"
    }
}
</script>
@endpush

@section('content')
<div class="space-y-8 pb-16">
    <!-- Breadcrumb -->
    <div class="border-b border-slate-200/80 bg-slate-50/50 py-3">
        <x-ui.container size="default">
            <x-breadcrumb :items="$breadcrumbs" />
        </x-ui.container>
    </div>

    <!-- Main Content Reader -->
    <section>
        <x-ui.container size="default">
            <div class="max-w-3xl mx-auto space-y-6 sm:space-y-8" x-data="{ fontSize: 'base' }">
                
                <!-- Title & Meta Header -->
                <header class="text-center space-y-3 pb-6 border-b border-amber-200">
                    <div class="inline-flex items-center gap-2">
                        <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $item->type === 'chalisa' ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-orange-100 text-orange-900 border border-orange-200' }}">
                            {{ $item->type === 'chalisa' ? '🚩 श्री चालीसा' : '🪔 पावन आरती' }}
                        </span>
                        @if($item->deity_name)
                            <span class="text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-200 px-3.5 py-1 rounded-full">
                                आराध्य: {{ $item->deity_name }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 font-serif tracking-tight">
                        {{ $item->title }}
                    </h1>

                    @if($item->author_name)
                        <p class="text-xs sm:text-sm text-slate-500 font-medium">
                            रचयिता: <span class="text-slate-800 font-semibold">{{ $item->author_name }}</span>
                        </p>
                    @endif

                    @if($item->submitter)
                        <div class="pt-1">
                            <x-contributor-badge :user="$item->submitter" role="प्रस्तुतकर्ता" />
                        </div>
                    @endif
                </header>

                <!-- Audio & Font Size Reader Controls (Sticky) -->
                <div class="sticky top-20 z-30 bg-white/95 backdrop-blur-md rounded-2xl border border-amber-200 p-3 sm:p-4 shadow-sm flex flex-wrap items-center justify-between gap-3">
                    <x-tts-controller targetTextId="devotional-stanzas" label="पाठ सुनें (Listen Audio)" />

                    <div class="flex items-center gap-1.5 text-xs text-slate-600">
                        <span class="text-[11px] text-slate-400 font-medium">फ़ॉन्ट आकार:</span>
                        <button type="button" @click="fontSize = 'sm'" :class="fontSize === 'sm' ? 'bg-amber-100 text-amber-900 font-bold' : 'hover:bg-slate-100'" class="px-2.5 py-1 rounded-lg transition">A-</button>
                        <button type="button" @click="fontSize = 'base'" :class="fontSize === 'base' ? 'bg-amber-100 text-amber-900 font-bold' : 'hover:bg-slate-100'" class="px-2.5 py-1 rounded-lg transition">A</button>
                        <button type="button" @click="fontSize = 'lg'" :class="fontSize === 'lg' ? 'bg-amber-100 text-amber-900 font-bold' : 'hover:bg-slate-100'" class="px-2.5 py-1 rounded-lg transition">A+</button>
                        <button type="button" @click="fontSize = 'xl'" :class="fontSize === 'xl' ? 'bg-amber-100 text-amber-900 font-bold' : 'hover:bg-slate-100'" class="px-2.5 py-1 rounded-lg transition">A++</button>
                    </div>
                </div>

                <!-- Devotional Lyrics / Stanzas Text -->
                <div 
                    id="devotional-stanzas"
                    :class="{
                        'text-sm leading-relaxed': fontSize === 'sm',
                        'text-base sm:text-lg leading-loose': fontSize === 'base',
                        'text-lg sm:text-xl leading-loose': fontSize === 'lg',
                        'text-xl sm:text-2xl leading-loose': fontSize === 'xl',
                    }"
                    class="bg-amber-50/40 rounded-3xl border border-amber-200/70 p-6 sm:p-10 font-serif text-slate-800 shadow-xs space-y-6 text-center select-text">
                    {!! nl2br(e($item->content)) !!}
                </div>

                <!-- Related Devotionals -->
                @if($related->isNotEmpty())
                    <div class="pt-8 border-t border-slate-200">
                        <h3 class="text-base font-bold text-slate-900 font-serif mb-4">
                            सम्बंधित पाठ व स्तुतियां (Related Recitations)
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($related as $rel)
                                <a href="{{ route($rel->type . '.show', ['slug' => $rel->slug]) }}" class="p-3.5 bg-white rounded-xl border border-slate-200 hover:border-amber-400 hover:shadow-xs transition block">
                                    <span class="text-xs text-amber-700 font-semibold uppercase">{{ $rel->type === 'chalisa' ? 'चालीसा' : 'आरती' }}</span>
                                    <h4 class="text-sm font-bold text-slate-800 truncate mt-0.5">{{ $rel->title }}</h4>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </x-ui.container>
    </section>
</div>
@endsection
