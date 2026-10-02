@extends('layouts.base')

@section('title', "अध्याय {$episode->episode_number}: {$episode->title} — {$series->title}")

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'बाल कहानियां', 'url' => route('stories.index')],
        ['label' => $series->title, 'url' => route('stories.series', ['series_slug' => $series->slug])],
        ['label' => "अध्याय {$episode->episode_number}", 'url' => null],
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

    <!-- Episode Reading Container -->
    <section>
        <x-ui.container size="default">
            <div class="max-w-3xl mx-auto space-y-6" x-data="{ fontSize: 'base' }">
                
                <!-- Episode Header -->
                <div class="text-center space-y-2 pb-4 border-b border-slate-200">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-100 text-emerald-900 inline-block">
                        {{ $series->title }} — अध्याय {{ $episode->episode_number }}
                    </span>
                    <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 font-serif tracking-tight">
                        {{ $episode->title }}
                    </h1>
                    @if($series->submitter)
                        <div class="pt-1">
                            <x-contributor-badge :user="$series->submitter" role="कथा प्रस्तोता" />
                        </div>
                    @endif
                </div>

                <!-- Dual Audio Mode Controller Area -->
                <div class="bg-white rounded-2xl border border-emerald-200 p-4 shadow-xs flex flex-wrap items-center justify-between gap-4">
                    
                    <!-- Left: Uploaded Narration Audio Player OR Browser TTS Fallback -->
                    @if($episode->has_uploaded_audio)
                        <!-- Mode 1: Uploaded Narration Audio Player -->
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <span class="text-xs font-bold text-emerald-900 flex items-center gap-1 shrink-0">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
                                </svg>
                                वाचन ऑडियो:
                            </span>
                            <audio controls src="{{ $episode->audio_url }}" class="h-9 w-full sm:w-64 accent-emerald-600 rounded-lg">
                                आपका ब्राउज़र ऑडियो टैग का समर्थन नहीं करता।
                            </audio>
                        </div>
                    @else
                        <!-- Mode 2: Central Browser SpeechSynthesis TTS Fallback -->
                        <div class="w-full sm:w-auto">
                            <x-tts-controller targetTextId="story-episode-content" label="कहानी सुनें (Listen)" />
                        </div>
                    @endif

                    <!-- Right: Font Size Adjustment -->
                    <div class="flex items-center gap-1.5 text-xs text-slate-600">
                        <span class="text-[11px] text-slate-400 font-medium">फ़ॉन्ट:</span>
                        <button type="button" @click="fontSize = 'sm'" :class="fontSize === 'sm' ? 'bg-emerald-100 text-emerald-900 font-bold' : 'hover:bg-slate-100'" class="px-2 py-1 rounded transition">A-</button>
                        <button type="button" @click="fontSize = 'base'" :class="fontSize === 'base' ? 'bg-emerald-100 text-emerald-900 font-bold' : 'hover:bg-slate-100'" class="px-2 py-1 rounded transition">A</button>
                        <button type="button" @click="fontSize = 'lg'" :class="fontSize === 'lg' ? 'bg-emerald-100 text-emerald-900 font-bold' : 'hover:bg-slate-100'" class="px-2 py-1 rounded transition">A+</button>
                        <button type="button" @click="fontSize = 'xl'" :class="fontSize === 'xl' ? 'bg-emerald-100 text-emerald-900 font-bold' : 'hover:bg-slate-100'" class="px-2 py-1 rounded transition">A++</button>
                    </div>

                </div>

                <!-- Story Text Content -->
                <div class="bg-amber-50/20 rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-xs">
                    <div 
                        id="story-episode-content"
                        :class="{
                            'text-base leading-relaxed': fontSize === 'sm',
                            'text-lg sm:text-xl leading-loose': fontSize === 'base',
                            'text-xl sm:text-2xl leading-loose': fontSize === 'lg',
                            'text-2xl sm:text-3xl leading-loose': fontSize === 'xl'
                        }"
                        class="prose prose-slate max-w-none font-serif text-slate-900 whitespace-pre-line leading-relaxed selection:bg-emerald-200">
                        {!! $episode->content !!}
                    </div>
                </div>

                <!-- Episode Navigation Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-slate-200">
                    @if($prevEpisode)
                        <a href="{{ route('stories.episode', ['series_slug' => $series->slug, 'episode_slug' => $prevEpisode->slug]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-xs transition">
                            &larr; पिछला अध्याय (Ep. {{ $prevEpisode->episode_number }})
                        </a>
                    @else
                        <div></div>
                    @endif

                    <a href="{{ route('stories.series', ['series_slug' => $series->slug]) }}" class="text-xs text-emerald-700 hover:text-emerald-800 font-bold">
                        सभी अध्याय देखें
                    </a>

                    @if($nextEpisode)
                        <a href="{{ route('stories.episode', ['series_slug' => $series->slug, 'episode_slug' => $nextEpisode->slug]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-emerald-600 bg-emerald-600 hover:bg-emerald-700 text-xs font-semibold text-white shadow-xs transition">
                            अगला अध्याय (Ep. {{ $nextEpisode->episode_number }}) &rarr;
                        </a>
                    @else
                        <div></div>
                    @endif
                </div>

            </div>
        </x-ui.container>
    </section>
</div>
@endsection
