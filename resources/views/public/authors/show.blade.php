@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $translation = $author->localized($locale);

    $name = $translation?->name ?? $author->slug;
    $biography = $translation?->biography;

    $portraitMedia = $author->media->where('collection_name', 'portrait')->first() ?? $author->media->first();
    $photoUrl = $portraitMedia ? asset('storage/' . $portraitMedia->file_name) : null;

    $initials = collect(explode(' ', $name))
        ->map(fn ($segment) => mb_substr($segment, 0, 1))
        ->take(2)
        ->join('');

    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'लेखक' : 'Authors', 'url' => route('home') . '#browse-authors'],
        ['label' => $name, 'url' => null],
    ];
@endphp

@section('content')
<div class="space-y-12 pb-16">
    <!-- Breadcrumb -->
    <div class="border-b border-slate-200/80 bg-slate-50/50 py-3">
        <x-ui.container size="default">
            <x-breadcrumb :items="$breadcrumbs" />
        </x-ui.container>
    </div>

    <!-- Author Profile Header -->
    <section>
        <x-ui.container size="default">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-2xs">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 lg:gap-8">
                    <!-- Author Photo / Avatar -->
                    <div class="relative h-28 w-28 sm:h-36 sm:w-36 shrink-0 overflow-hidden rounded-full bg-amber-100 border-2 border-amber-200 text-brand-800 flex items-center justify-center font-bold text-3xl shadow-xs">
                        @if ($photoUrl)
                            <img src="{{ $photoUrl }}" alt="Portrait of {{ $name }}" class="h-full w-full object-cover" loading="eager" />
                        @else
                            <span>{{ $initials }}</span>
                        @endif
                    </div>

                    <!-- Author Details -->
                    <div class="flex-1 text-center sm:text-left">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-800 uppercase tracking-wider mb-1">
                                    <span>साहित्यकार • Author Profile</span>
                                </div>
                                <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                                    {{ $name }}
                                </h1>
                            </div>

                            <div>
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 border border-slate-200">
                                    {{ $books->total() }} Published {{ $books->total() === 1 ? 'Book' : 'Books' }}
                                </span>
                            </div>
                        </div>

                        @if ($biography)
                            <div class="mt-4 text-sm sm:text-base text-slate-600 leading-relaxed max-w-4xl">
                                {!! nl2br(e($biography)) !!}
                            </div>
                        @else
                            <p class="mt-4 text-xs text-slate-400 italic">
                                Extended biographical archive profile undergoing research compilation.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>

    <!-- Published Works by Author -->
    <section>
        <x-ui.container size="default">
            <x-ui.section-header 
                title="Published Works (रचनाएँ एवं पुस्तकें)"
                subtitle="All digitized titles and publications by {{ $name }} available in our library."
            />

            @if ($books->isNotEmpty())
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    @foreach ($books as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $books->links() }}
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/50 p-12 text-center">
                    <p class="text-sm text-slate-600 font-medium">
                        No published titles currently cataloged for this author.
                    </p>
                </div>
            @endif
        </x-ui.container>
    </section>

    <!-- Related / Other Prominent Authors -->
    @if ($relatedAuthors->isNotEmpty())
        <section class="border-t border-slate-200/80 pt-10">
            <x-ui.container size="default">
                <x-ui.section-header 
                    title="Other Prominent Authors (अन्य प्रमुख लेखक)"
                    subtitle="Explore contemporary and foundational literary voices."
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($relatedAuthors as $relAuthor)
                        <x-author-card :author="$relAuthor" />
                    @endforeach
                </div>
            </x-ui.container>
        </section>
    @endif
</div>
@endsection
