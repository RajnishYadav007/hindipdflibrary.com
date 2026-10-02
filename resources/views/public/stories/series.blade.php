@extends('layouts.base')

@section('title', "{$series->title} — बाल कथाएं (Children's Stories)")

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'बाल कहानियां', 'url' => route('stories.index')],
        ['label' => $series->title, 'url' => null],
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

    <!-- Series Overview Header -->
    <section>
        <x-ui.container size="default">
            <div class="bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-900 text-white rounded-3xl p-6 sm:p-10 shadow-xl relative overflow-hidden">
                <div class="max-w-2xl space-y-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-700/80 border border-emerald-500/40 inline-block">
                        आयु वर्ग: {{ $series->age_group }} वर्ष
                    </span>
                    <h1 class="text-2xl sm:text-4xl font-extrabold font-serif tracking-tight">
                        {{ $series->title }}
                    </h1>
                    @if($series->description)
                        <p class="text-sm sm:text-base text-emerald-100 leading-relaxed">
                            {{ $series->description }}
                        </p>
                    @endif

                    @if($series->submitter)
                        <div class="pt-2">
                            <x-contributor-badge :user="$series->submitter" role="कथा प्रस्तोता" />
                        </div>
                    @endif
                </div>
            </div>
        </x-ui.container>
    </section>

    <!-- Episodes Tracklist -->
    <section>
        <x-ui.container size="default">
            <div class="max-w-4xl mx-auto space-y-4">
                <h2 class="text-xl font-bold text-slate-900 flex items-center justify-between pb-3 border-b">
                    <span>इस श्रृंखला के सभी अध्याय (Episodes)</span>
                    <span class="text-xs font-normal text-slate-500 font-mono">{{ $episodes->count() }} अध्याय</span>
                </h2>

                @if($episodes->isEmpty())
                    <p class="text-sm text-slate-500 py-8 text-center bg-slate-50 rounded-2xl border">
                        इस श्रृंखला में अभी अध्याय संकलित किए जा रहे हैं।
                    </p>
                @else
                    <div class="space-y-3">
                        @foreach($episodes as $ep)
                            <a href="{{ route('stories.episode', ['series_slug' => $series->slug, 'episode_slug' => $ep->slug]) }}" class="flex items-center justify-between p-4 sm:p-5 rounded-2xl border border-slate-200 bg-white hover:border-emerald-500 hover:shadow-md transition group">
                                <div class="flex items-center gap-4 min-w-0">
                                    <span class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-mono font-bold text-sm flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition">
                                        {{ $ep->episode_number }}
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="text-sm sm:text-base font-bold text-slate-900 truncate group-hover:text-emerald-700 transition">
                                            {{ $ep->title }}
                                        </h3>
                                        <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500">
                                            @if($ep->has_uploaded_audio)
                                                <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                                                    ऑडियो उपलब्ध
                                                </span>
                                            @else
                                                <span class="text-slate-400">आवाज़ सुनें (TTS)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-xs font-semibold text-emerald-700 group-hover:translate-x-1 transition">पढ़ें व सुनें &rarr;</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </x-ui.container>
    </section>
</div>
@endsection
