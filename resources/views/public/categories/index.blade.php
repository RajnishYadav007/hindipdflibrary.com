@extends('layouts.base')

@php
    $locale = app()->getLocale();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $locale === 'hi' ? 'श्रेणियाँ' : 'Categories', 'url' => null],
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

    <!-- Categories Header -->
    <section>
        <x-ui.container size="default">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200 pb-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-800 mb-2">
                        <span>{{ $categories->count() }}</span>
                        <span>{{ $locale === 'hi' ? 'मुख्य श्रेणियाँ' : 'Main Subjects' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                        {{ $locale === 'hi' ? 'विषय एवं श्रेणियाँ (Subject Categories)' : 'Subject Categories' }}
                    </h1>
                    <p class="mt-1.5 text-sm text-slate-500 max-w-2xl">
                        {{ $locale === 'hi' ? 'विषय, विधा और अध्ययन शाखा के अनुसार हिन्दी साहित्य और ज्ञान-ग्रन्थ खोजें।' : 'Browse our digital library organized by literary genres, academic subjects, and disciplines.' }}
                    </p>
                </div>

                <a 
                    href="{{ route('genres.index') }}" 
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700 hover:text-brand-800 transition"
                >
                    <span>{{ $locale === 'hi' ? 'विधाएँ एवं साहित्यिक रूप देखें &rarr;' : 'Explore Genres & Forms &rarr;' }}</span>
                </a>
            </div>

            <!-- Categories Grid -->
            <div class="mt-8">
                @if ($categories->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($categories as $category)
                            @php
                                $catName = $category->translations->where('locale', $locale)->first()?->name
                                    ?? $category->translations->where('locale', 'en')->first()?->name
                                    ?? $category->slug;
                                $catDesc = $category->translations->where('locale', $locale)->first()?->description
                                    ?? $category->translations->where('locale', 'en')->first()?->description;
                            @endphp
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs hover:shadow-xs transition">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-brand-700 border border-amber-200">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-base font-bold text-slate-900 hover:text-brand-800 transition">
                                                <a href="{{ route('categories.show', $category->slug) }}">
                                                    {{ $catName }}
                                                </a>
                                            </h2>
                                            <span class="text-xs text-slate-400">
                                                {{ $category->books_count }} {{ $locale === 'hi' ? 'पुस्तकें' : 'Books' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if ($catDesc)
                                    <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 mb-4">
                                        {{ $catDesc }}
                                    </p>
                                @endif

                                <!-- Subcategories pills if present -->
                                @if ($category->children && $category->children->isNotEmpty())
                                    <div class="border-t border-slate-100 pt-3">
                                        <span class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">
                                            {{ $locale === 'hi' ? 'उप-श्रेणियाँ' : 'Subcategories' }}
                                        </span>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($category->children as $child)
                                                @php
                                                    $childName = $child->translations->where('locale', $locale)->first()?->name
                                                        ?? $child->translations->where('locale', 'en')->first()?->name
                                                        ?? $child->slug;
                                                @endphp
                                                <a 
                                                    href="{{ route('categories.show', $child->slug) }}" 
                                                    class="inline-flex items-center gap-1 rounded-md bg-slate-50 px-2 py-1 text-xs text-slate-600 hover:bg-brand-50 hover:text-brand-800 border border-slate-200/80 transition"
                                                >
                                                    <span>{{ $childName }}</span>
                                                    <span class="text-[10px] text-slate-400">({{ $child->books_count }})</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
                        <p class="text-sm text-slate-500">
                            {{ $locale === 'hi' ? 'कोई श्रेणी उपलब्ध नहीं है।' : 'No categories found.' }}
                        </p>
                    </div>
                @endif
            </div>
        </x-ui.container>
    </section>
</div>
@endsection
