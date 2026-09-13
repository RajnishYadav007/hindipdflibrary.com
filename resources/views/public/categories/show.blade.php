@extends('layouts.base')

@php
    $translation = $category->translations->where('locale', 'hi')->first() 
        ?? $category->translations->where('locale', 'en')->first() 
        ?? $category->translations->first();

    $name = $translation?->name ?? $category->slug;
    $description = $translation?->description;

    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Categories', 'url' => route('home') . '#browse-categories'],
    ];

    if ($category->parent) {
        $parentTrans = $category->parent->translations->where('locale', 'hi')->first() 
            ?? $category->parent->translations->where('locale', 'en')->first() 
            ?? $category->parent->translations->first();
        $parentName = $parentTrans?->name ?? $category->parent->slug;
        $breadcrumbs[] = ['label' => $parentName, 'url' => route('categories.show', $category->parent->slug)];
    }

    $breadcrumbs[] = ['label' => $name, 'url' => null];
@endphp

@section('title', $name . ' — Books & Literature | Hindi PDF Library')

@section('content')
<div class="space-y-12 pb-16">
    <!-- Breadcrumb -->
    <div class="border-b border-slate-200/80 bg-slate-50/50 py-3">
        <x-ui.container size="default">
            <x-breadcrumb :items="$breadcrumbs" />
        </x-ui.container>
    </div>

    <!-- Category Header -->
    <section>
        <x-ui.container size="default">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-10 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-800 uppercase tracking-wider mb-1">
                            <span>साहित्य श्रेणी • Category Catalog</span>
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

                @if ($description)
                    <p class="mt-4 text-sm sm:text-base text-slate-600 leading-relaxed max-w-4xl">
                        {{ $description }}
                    </p>
                @endif

                <!-- Subcategories If Available -->
                @if ($subcategories->isNotEmpty())
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                            Subcategories (उप-श्रेणियाँ)
                        </h2>
                        <div class="flex flex-wrap gap-2.5">
                            @foreach ($subcategories as $sub)
                                @php
                                    $subTrans = $sub->translations->where('locale', 'hi')->first() 
                                        ?? $sub->translations->where('locale', 'en')->first() 
                                        ?? $sub->translations->first();
                                    $subName = $subTrans?->name ?? $sub->slug;
                                @endphp
                                <a 
                                    href="{{ route('categories.show', $sub->slug) }}" 
                                    class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-700 hover:border-brand-500 hover:bg-white hover:text-brand-800 transition shadow-2xs"
                                >
                                    <span>{{ $subName }}</span>
                                    <span class="rounded-full bg-white px-1.5 py-0.2 text-[10px] font-bold text-slate-500 border border-slate-200">
                                        {{ $sub->books_count }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </x-ui.container>
    </section>

    <!-- Published Books in Category -->
    <section>
        <x-ui.container size="default">
            <x-ui.section-header 
                title="Books in {{ $name }}"
                subtitle="Browse all available digital and reference editions categorized under {{ $name }}."
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
                        No published titles currently cataloged in this category.
                    </p>
                </div>
            @endif
        </x-ui.container>
    </section>
</div>
@endsection
