@extends('layouts.base')

@section('title', "{$pageTitle} — हिन्दी पीडीएफ पुस्तकालय")

@php
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $type === 'chalisa' ? 'चालीसा संग्रह' : 'आरती संग्रह', 'url' => null],
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

    <!-- Header -->
    <section>
        <x-ui.container size="default">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full {{ $type === 'chalisa' ? 'bg-amber-50 text-amber-900 border-amber-200' : 'bg-orange-50 text-orange-900 border-orange-200' }} px-2.5 py-0.5 text-xs font-semibold mb-2 border">
                        <span>{{ $items->total() }}</span>
                        <span>{{ $type === 'chalisa' ? 'चालीसा पाठ उपलब्ध' : 'आरतियां उपलब्ध' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 font-serif">
                        {{ $pageTitle }}
                    </h1>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ $pageDescription }}
                    </p>
                </div>

                <!-- Search -->
                <form action="{{ $type === 'chalisa' ? route('chalisa.index') : route('aarti.index') }}" method="GET" class="flex items-center gap-2">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="देवता या पाठ खोजें..."
                        class="rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:ring-amber-500 w-48 sm:w-60">
                    <button type="submit" class="px-4 py-2 {{ $type === 'chalisa' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-orange-600 hover:bg-orange-700' }} text-white rounded-xl text-xs font-semibold shadow-xs">
                        खोजें
                    </button>
                </form>
            </div>
        </x-ui.container>
    </section>

    <!-- Devotional Grid -->
    <section>
        <x-ui.container size="default">
            @if($items->isEmpty())
                <div class="text-center py-16 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                    <h3 class="text-base font-bold text-slate-700">कोई प्रविष्टि नहीं मिली</h3>
                    <p class="text-xs text-slate-500 mt-1">कृपया अन्य खोज शब्द का प्रयास करें।</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($items as $dev)
                        @php
                            $route = $dev->type === 'chalisa' 
                                ? route('chalisa.show', ['slug' => $dev->slug]) 
                                : route('aarti.show', ['slug' => $dev->slug]);
                        @endphp
                        <a href="{{ $route }}" class="group flex flex-col justify-between bg-white rounded-2xl border border-slate-200/90 p-5 hover:border-amber-400 hover:shadow-md transition">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $dev->type === 'chalisa' ? 'bg-amber-100 text-amber-900' : 'bg-orange-100 text-orange-900' }}">
                                        {{ $dev->type === 'chalisa' ? '🚩 चालीसा' : '🪔 आरती' }}
                                    </span>
                                    @if($dev->deity_name)
                                        <span class="text-xs font-medium text-slate-500">{{ $dev->deity_name }}</span>
                                    @endif
                                </div>
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-amber-700 transition font-serif">
                                    {{ $dev->title }}
                                </h3>
                                @if($dev->description)
                                    <p class="text-xs text-slate-600 mt-2 line-clamp-2 leading-relaxed">
                                        {{ $dev->description }}
                                    </p>
                                @endif
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-amber-700 font-semibold">
                                <span>पाठ पढ़ें व सुनें</span>
                                <span class="group-hover:translate-x-1 transition">&rarr;</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $items->links() }}
                </div>
            @endif
        </x-ui.container>
    </section>
</div>
@endsection
