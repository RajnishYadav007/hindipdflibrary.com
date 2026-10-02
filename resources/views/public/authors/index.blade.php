@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'लेखक सूची' : 'Authors Directory', 'url' => null],
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

    <!-- Authors Header -->
    <section>
        <x-ui.container size="default">
            <div class="border-b border-slate-200 pb-6">
                <div class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-800 mb-2">
                    <span>{{ $authors->total() }}</span>
                    <span>{{ $locale === 'hi' ? 'लेखक सूचीबद्ध' : 'Authors Listed' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    {{ $locale === 'hi' ? 'प्रतिष्ठित लेखक एवं रचनाकार' : 'Authors Directory' }}
                </h1>
                <p class="mt-1.5 text-sm text-slate-500 max-w-2xl">
                    {{ $locale === 'hi' ? 'हिन्दी साहित्य, दर्शन, नाटक, एवं कविता के मूर्धन्य विद्वानों व लेखकों की रचनाएँ खोजें।' : 'Explore literary masters, poets, scholars, and novelists across Hindi and Indian literature.' }}
                </p>
            </div>

            <!-- Authors Grid -->
            <div class="mt-8">
                @if ($authors->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($authors as $author)
                            <x-author-card :author="$author" />
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12 flex justify-center">
                        {{ $authors->links() }}
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
                        <p class="text-sm text-slate-500">
                            {{ $locale === 'hi' ? 'कोई लेखक उपलब्ध नहीं है।' : 'No authors found in the directory.' }}
                        </p>
                    </div>
                @endif
            </div>
        </x-ui.container>
    </section>
</div>
@endsection
