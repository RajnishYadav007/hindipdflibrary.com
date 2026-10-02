@extends('layouts.base')

@section('title', "{$audiobook->title} — सम्पूर्ण ऑडियोबुक (Hindi Audiobook)")

@php
    $locale = app()->getLocale();
    $isSingleTrack = $audiobook->playable_tracks->count() === 1 && $audiobook->playable_tracks->first()['id'] === 'single';
    $breadcrumbs = [
        ['label' => ($locale === 'hi' ? 'ऑडियोबुक्स' : 'Audiobooks'), 'url' => route('audiobooks.index')],
        ['label' => $audiobook->title, 'url' => null],
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

    <!-- Main Player Section -->
    <section>
        <x-ui.container size="default">
            <x-audiobook-player :audiobook="$audiobook" :chapters="$chapters" />
        </x-ui.container>
    </section>

    <!-- Audiobook Description & Metadata Grid -->
    <section>
        <x-ui.container size="default">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left: Synopsis & Notes -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-xs">
                        <h2 class="text-lg font-bold text-slate-900 font-serif mb-3">
                            ऑडियोबुक परिचय (About the Audiobook)
                        </h2>
                        @if($audiobook->description)
                            <div class="prose prose-slate text-sm leading-relaxed text-slate-700">
                                {!! nl2br(e($audiobook->description)) !!}
                            </div>
                        @else
                            <p class="text-sm text-slate-500 italic">इस ऑडियोबुक का विस्तृत विवरण अभी संकलित किया जा रहा है।</p>
                        @endif

                        @if($audiobook->book)
                            <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-between">
                                <div>
                                    <h4 class="text-xs font-bold text-amber-950">मूल पुस्तक भी उपलब्ध है</h4>
                                    <p class="text-xs text-amber-800">क्या आप इस पुस्तक का पीडीएफ संस्करण पढ़ना या डाउनलोड करना चाहते हैं?</p>
                                </div>
                                <a href="{{ route('books.show', ['slug' => $audiobook->book->slug]) }}" class="px-3.5 py-1.5 bg-amber-700 hover:bg-amber-800 text-white rounded-lg text-xs font-bold shrink-0 shadow-xs">
                                    पुस्तक देखें &rarr;
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right: Legal & Metadata Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs text-xs space-y-3">
                        <h3 class="font-bold text-sm text-slate-900 border-b pb-2">ऑडियो विवरण (Metadata)</h3>
                        
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">वाचक (Narrator):</span>
                            <span class="font-semibold text-slate-800">{{ $audiobook->narrator ?: 'अज्ञात' }}</span>
                        </div>

                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">संरचना (Tracks):</span>
                            <span class="font-semibold text-slate-800">
                                @if($isSingleTrack)
                                    एकल ट्रैक (Single Track)
                                @elseif($chapters->count() > 0)
                                    {{ $chapters->count() }} अध्याय
                                @else
                                    उपलब्ध नहीं
                                @endif
                            </span>
                        </div>

                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">कुल अवधि (Duration):</span>
                            <span class="font-semibold text-slate-800 font-mono">
                                {{ $audiobook->formatted_duration ?: 'अवधि उपलब्ध नहीं' }}
                            </span>
                        </div>

                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">भाषा:</span>
                            <span class="font-semibold text-slate-800">{{ $audiobook->language?->name ?? 'हिन्दी' }}</span>
                        </div>

                        @if($audiobook->source_type)
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-slate-500">स्रोत (Source):</span>
                                <span class="font-semibold text-slate-800">
                                    @if($audiobook->source_type === 'archive_org')
                                        Internet Archive / LibriVox
                                    @else
                                        {{ ucfirst($audiobook->source_type) }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        <div class="pt-2">
                            <span class="text-slate-500 block mb-1">कॉपीराइट व लाइसेंस:</span>
                            <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-mono text-[11px] inline-block">
                                {{ $audiobook->copyright_status ?: 'Pending Review' }}
                            </span>
                            @if($audiobook->license_type)
                                <p class="text-[10px] text-slate-400 mt-1 truncate" title="{{ $audiobook->license_type }}">
                                    लाइसेंस: {{ Str::limit($audiobook->license_type, 40) }}
                                </p>
                            @endif
                        </div>

                        @if($audiobook->submitter)
                            <div class="pt-3 border-t border-slate-100">
                                <x-contributor-badge :user="$audiobook->submitter" role="प्रस्तोता" />
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </x-ui.container>
    </section>
</div>
@endsection
