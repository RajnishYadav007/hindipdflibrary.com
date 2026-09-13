@props([
    'books' => [],
    'title' => 'Related Titles & Recommendations',
    'subtitle' => 'Explore similar books in this genre and language.',
])

<section {{ $attributes->merge(['class' => 'mt-12']) }}>
    <x-ui.section-header 
        :title="$title" 
        :subtitle="$subtitle"
    />

    @if (count($books) > 0)
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            @foreach ($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>
    @else
        <x-ui.empty-state 
            title="No related books available"
            description="We haven't indexed other titles directly related to this selection yet."
        />
    @endif
</section>
