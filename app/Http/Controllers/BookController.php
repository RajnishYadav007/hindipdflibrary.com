<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\Discovery\BookDiscoveryService;
use App\Services\SEO\SeoManager;
use Illuminate\Contracts\View\View;

class BookController extends Controller
{
    /**
     * Display the specified book detail page.
     */
    public function show(string $localeOrSlug, ?string $slug = null): View
    {
        $slug = $slug ?? $localeOrSlug;

        $book = Book::where('slug', $slug)
            ->with([
                'translations',
                'authors.translations',
                'authors.media',
                'categories.translations',
                'genres.translations',
                'languages',
                'originalLanguage',
                'media',
            ])
            ->firstOrFail();

        if (! $book->isPublished()) {
            abort(404);
        }

        // Increment view count quietly
        $book->incrementQuietly('view_count');

        // Approved reviews paginated
        $reviews = $book->reviews()
            ->approved()
            ->with('user')
            ->latest()
            ->paginate(10);

        $discovery = app(BookDiscoveryService::class);
        $authorBooks = $discovery->relatedByAuthor($book, 4);
        $relatedBooks = $discovery->relatedByCategory($book, 4, $authorBooks);

        // Digital media asset (e.g. PDF)
        $pdfMedia = $book->media->where('collection_name', 'pdf')->first()
            ?? $book->media->filter(fn ($m) => str_contains($m->mime_type ?? '', 'pdf'))->first();

        // 1-5 star rating distribution breakdown (from approved reviews)
        $ratingDistribution = $book->reviews()
            ->approved()
            ->selectRaw('rating, count(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // Semantic SEO metadata and Schema.org JSON-LD
        $seo = SeoManager::forBook($book, app()->getLocale());

        return view('public.books.show', compact(
            'book',
            'reviews',
            'authorBooks',
            'relatedBooks',
            'pdfMedia',
            'ratingDistribution',
            'seo'
        ));
    }
}
