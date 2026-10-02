@extends('layouts.base')

@section('title', 'बाल साहित्य व कहानियां (Children\'s Stories & Folk Tales) — हिन्दी पीडीएफ पुस्तकालय')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'बाल कहानियां (Children\'s Stories)', 'url' => null],
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
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-900 border border-emerald-200 px-2.5 py-0.5 text-xs font-semibold mb-2">
                        <span>{{ $seriesList->total() }}</span>
                        <span>कथा श्रृंखलाएं उपलब्ध</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 font-serif">
                        रोचक बाल साहित्य व कहानियां (Children's Stories)
                    </h1>
                    <p class="mt-1 text-sm text-slate-600">
                        पंचतंत्र, हितोपदेश, जातक कथाएं एवं प्रेरणादायक कहानियां — बच्चों के लिए सरल भाषा, ऑडियो वाचन एवं आकर्षक प्रस्तुति।
                    </p>
                </div>

                <!-- Age Filter Buttons -->
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach($ageGroups as $key => $label)
                        @php
                            $isActive = (request('age_group') == $key) || (!request('age_group') && $key === 'all');
                        @endphp
                        <a href="{{ route('stories.index', ['age_group' => $key]) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ $isActive ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </x-ui.container>
    </section>

    <!-- Series Grid -->
    <section>
        <x-ui.container size="default">
            @if($seriesList->isEmpty())
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                    <h3 class="text-base font-bold text-slate-700">कोई कहानी श्रृंखला नहीं मिली</h3>
                    <p class="text-xs text-slate-500 mt-1">कृपया अन्य आयु वर्ग या खोज शब्द चुनें।</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($seriesList as $series)
                        <a href="{{ route('stories.series', ['series_slug' => $series->slug]) }}" class="group flex flex-col bg-white rounded-2xl border border-slate-200/90 overflow-hidden hover:border-emerald-500 hover:shadow-lg transition">
                            <div class="w-full aspect-video bg-gradient-to-tr from-emerald-800 to-teal-600 relative p-4 flex flex-col justify-between text-white">
                                <span class="self-start px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-white/20 backdrop-blur-xs">
                                    {{ $series->age_group }} वर्ष के लिए
                                </span>
                                <div>
                                    <h3 class="text-base font-bold line-clamp-2">{{ $series->title }}</h3>
                                    <span class="text-xs text-emerald-200 mt-0.5 block font-mono">
                                        {{ $series->publishedEpisodes->count() }} अध्याय उपलब्ध
                                    </span>
                                </div>
                            </div>

                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                    {{ $series->description ?: 'रोचक बाल कथाओं का संग्रह।' }}
                                </p>
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-emerald-700 font-semibold">
                                    <span>कहानियां पढ़ें व सुनें</span>
                                    <span class="group-hover:translate-x-1 transition">&rarr;</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $seriesList->links() }}
                </div>
            @endif
        </x-ui.container>
    </section>
</div>
@endsection
