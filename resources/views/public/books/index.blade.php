@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'सभी पुस्तकें' : 'All Books', 'url' => null],
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

    <!-- Catalog Header & Filter Bar -->
    <section>
        <x-ui.container size="default">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-800 mb-2">
                        <span>{{ $books->total() }}</span>
                        <span>{{ $locale === 'hi' ? 'पुस्तकें उपलब्ध' : 'Books Available' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                        {{ $locale === 'hi' ? 'पुस्तकालय संग्रह (All Books)' : 'Complete Books Catalog' }}
                    </h1>
                    <p class="mt-1.5 text-sm text-slate-500 max-w-2xl">
                        {{ $locale === 'hi' ? 'हिन्दी साहित्य के कालजयी उपन्यास, कहानियाँ, काव्य एवं विचार ग्रन्थ।' : 'Explore our complete digital repository of classic Hindi literature and texts.' }}
                    </p>
                </div>

                <!-- Sorting Bar -->
                <div class="flex items-center gap-2 self-start md:self-auto">
                    <span class="text-xs font-medium text-slate-400">{{ $locale === 'hi' ? 'क्रमबद्ध:' : 'Sort by:' }}</span>
                    <div class="inline-flex rounded-lg border border-slate-200 bg-white p-0.5 shadow-2xs">
                        <a 
                            href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" 
                            class="px-2.5 py-1 text-xs font-medium rounded-md transition {{ $sort === 'popular' ? 'bg-brand-50 text-brand-800 font-semibold' : 'text-slate-600 hover:text-slate-900' }}"
                        >
                            {{ $locale === 'hi' ? 'लोकप्रिय' : 'Popular' }}
                        </a>
                        <a 
                            href="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}" 
                            class="px-2.5 py-1 text-xs font-medium rounded-md transition {{ $sort === 'rating' ? 'bg-brand-50 text-brand-800 font-semibold' : 'text-slate-600 hover:text-slate-900' }}"
                        >
                            {{ $locale === 'hi' ? 'रेटिंग' : 'Rating' }}
                        </a>
                        <a 
                            href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" 
                            class="px-2.5 py-1 text-xs font-medium rounded-md transition {{ $sort === 'newest' ? 'bg-brand-50 text-brand-800 font-semibold' : 'text-slate-600 hover:text-slate-900' }}"
                        >
                            {{ $locale === 'hi' ? 'नवीनतम' : 'Newest' }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="mt-8">
                @if ($books->isNotEmpty())
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                        @foreach ($books as $book)
                            <x-book-card :book="$book" />
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 flex justify-center">
                        {{ $books->links() }}
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
                        <p class="text-sm text-slate-500">
                            {{ $locale === 'hi' ? 'कोई पुस्तक उपलब्ध नहीं है।' : 'No books found in the catalog at this time.' }}
                        </p>
                    </div>
                @endif
            </div>
        </x-ui.container>
    </section>
</div>
@endsection
