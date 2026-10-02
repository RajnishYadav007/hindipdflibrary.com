@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'विधाएँ एवं रूप' : 'Genres & Forms', 'url' => null],
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

    <!-- Genres Header -->
    <section>
        <x-ui.container size="default">
            <div class="border-b border-slate-200 pb-6">
                <div class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-800 mb-2">
                    <span>{{ $genres->count() }}</span>
                    <span>{{ $locale === 'hi' ? 'विधाएँ' : 'Genres' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    {{ $locale === 'hi' ? 'साहित्यिक विधाएँ एवं रूप (Genres & Forms)' : 'Genres & Literary Forms' }}
                </h1>
                <p class="mt-1.5 text-sm text-slate-500 max-w-2xl">
                    {{ $locale === 'hi' ? 'उपन्यास, कहानी, कविता, नाटक, निबंध, जीवनी एवं आलोचना साहित्य की विभिन्न विधाओं में पुस्तकें खोजें।' : 'Browse books classified by creative genres including poetry, drama, novels, essays, and memoirs.' }}
                </p>
            </div>

            <!-- Genres Grid -->
            <div class="mt-8">
                @if ($genres->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($genres as $genre)
                            @php
                                $genreName = $genre->translations->where('locale', $locale)->first()?->name
                                    ?? $genre->translations->where('locale', 'en')->first()?->name
                                    ?? $genre->slug;
                                $genreDesc = $genre->translations->where('locale', $locale)->first()?->description
                                    ?? $genre->translations->where('locale', 'en')->first()?->description;
                            @endphp
                            <div class="group rounded-xl border border-slate-200 bg-white p-4 shadow-2xs hover:border-brand-300 hover:shadow-xs transition">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-brand-800 transition">
                                        <a href="{{ route('search', ['genre' => $genre->slug]) }}">
                                            {{ $genreName }}
                                        </a>
                                    </h3>
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                                        {{ $genre->books_count }} {{ $locale === 'hi' ? 'पुस्तकें' : 'Books' }}
                                    </span>
                                </div>
                                @if ($genreDesc)
                                    <p class="mt-1.5 text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                        {{ $genreDesc }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
                        <p class="text-sm text-slate-500">
                            {{ $locale === 'hi' ? 'कोई विधा उपलब्ध नहीं है।' : 'No genres found.' }}
                        </p>
                    </div>
                @endif
            </div>
        </x-ui.container>
    </section>
</div>
@endsection
