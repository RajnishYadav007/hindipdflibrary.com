<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Contracts\View\View;

class BookController extends Controller
{
    /**
     * Display the specified book detail page.
     */
    public function show(string $slug): View
    {
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

        // More books by this author
        $authorIds = $book->authors->pluck('id');
        $authorBooks = $authorIds->isNotEmpty()
            ? Book::published()
                ->where('id', '!=', $book->id)
                ->whereHas('authors', fn ($q) => $q->whereIn('authors.id', $authorIds))
                ->with(['translations', 'authors.translations', 'categories.translations', 'media'])
                ->take(4)
                ->get()
            : collect();

        // Related books by category
        $categoryIds = $book->categories->pluck('id');
        $excludeIds = $authorBooks->pluck('id')->push($book->id);
        $relatedBooks = $categoryIds->isNotEmpty()
            ? Book::published()
                ->whereNotIn('id', $excludeIds)
                ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
                ->with(['translations', 'authors.translations', 'categories.translations', 'media'])
                ->take(4)
                ->get()
            : collect();

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

        return view('public.books.show', compact(
            'book',
            'reviews',
            'authorBooks',
            'relatedBooks',
            'pdfMedia',
            'ratingDistribution'
        ));
    }
}
