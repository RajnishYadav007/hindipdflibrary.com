@extends('layouts.base')

@section('title', 'ऑडियोबुक्स संग्रह (Hindi Audiobooks Catalog) — हिन्दी पीडीएफ पुस्तकालय')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => ($locale === 'hi' ? 'ऑडियोबुक्स' : 'Audiobooks'), 'url' => null],
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

    <!-- Catalog Header -->
    <section>
        <x-ui.container size="default">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-800 mb-2 border border-amber-200">
                        <span>{{ $audiobooks->total() }}</span>
                        <span>ऑडियोबुक्स उपलब्ध</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 font-serif">
                        हिन्दी ऑडियोबुक्स संग्रह (Audiobooks)
                    </h1>
                    <p class="mt-1 text-sm text-slate-600">
                        सर्वश्रेष्ठ हिन्दी उपन्यास, कहानियां व आध्यात्मिक ग्रंथों का स्पष्ट स्वर वाचन, अध्यायवार प्लेबैक के साथ।
                    </p>
                </div>

                <!-- Search & Filters Form -->
                <form action="{{ route('audiobooks.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="शीर्षक या वाचक खोजें..."
                        class="rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:border-amber-500 focus:ring-amber-500 w-48 sm:w-60">
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                        खोजें
                    </button>
                    @if(request()->filled('search') || request()->filled('language'))
                        <a href="{{ route('audiobooks.index') }}" class="px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-600 hover:bg-slate-100">रीसेट</a>
                    @endif
                </form>
            </div>
        </x-ui.container>
    </section>

    <!-- Audiobooks Grid -->
    <section>
        <x-ui.container size="default">
            @if($audiobooks->isEmpty())
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                    <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"/>
                    </svg>
                    <h3 class="text-base font-bold text-slate-700">कोई ऑडियोबुक नहीं मिली</h3>
                    <p class="text-xs text-slate-500 mt-1">कृपया अन्य खोज शब्द का प्रयास करें।</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 sm:gap-6">
                    @foreach($audiobooks as $ab)
                        <a href="{{ route('audiobooks.show', ['slug' => $ab->slug]) }}" class="group flex flex-col bg-white rounded-2xl border border-slate-200/80 p-3 hover:border-amber-400 hover:shadow-lg transition duration-200">
                            <!-- Cover -->
                            <div class="w-full aspect-[3/4] rounded-xl overflow-hidden bg-slate-100 relative mb-3">
                                @if($ab->cover_url)
                                    <img src="{{ $ab->cover_url }}" alt="{{ $ab->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center bg-gradient-to-tr from-amber-900/40 to-slate-800 text-white">
                                        <svg class="w-8 h-8 text-amber-400 mb-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/>
                                        </svg>
                                        <span class="text-[11px] font-bold line-clamp-2">{{ $ab->title }}</span>
                                    </div>
                                @endif
                                <div class="absolute bottom-2 right-2 bg-neutral-900/90 backdrop-blur-xs text-white text-[10px] font-mono px-2 py-0.5 rounded-md flex items-center gap-1">
                                    <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $ab->formatted_duration ?: ($ab->publishedChapters->count() > 0 ? $ab->publishedChapters->count() . ' अध्याय' : ($ab->hasPlayableSource() ? 'सम्पूर्ण ऑडियो' : 'स्रोत अनुपलब्ध')) }}</span>
                                </div>
                            </div>

                            <!-- Meta -->
                            <h3 class="text-xs sm:text-sm font-bold text-slate-900 line-clamp-2 group-hover:text-amber-700 transition">
                                {{ $ab->title }}
                            </h3>
                            @if($ab->narrator)
                                <p class="text-[11px] text-slate-500 mt-1 truncate">
                                    स्वर: <span class="text-slate-700 font-medium">{{ $ab->narrator }}</span>
                                </p>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $audiobooks->links() }}
                </div>
            @endif
        </x-ui.container>
    </section>
</div>
@endsection
