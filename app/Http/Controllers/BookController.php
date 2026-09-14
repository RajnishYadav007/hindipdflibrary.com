<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\Analytics\AnalyticsService;
use App\Services\Discovery\BookDiscoveryService;
use App\Services\SEO\SeoManager;
use App\Support\Analytics\AnalyticsEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

        // Capture book_view analytics event safely
        app(AnalyticsService::class)->captureFromRequest(request(), AnalyticsEvent::BOOK_VIEW, [
            'book_id' => $book->id,
        ]);

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

    /**
     * Read action capturing book_read analytics.
     */
    public function read(Request $request, string $localeOrSlug, ?string $slug = null): View|RedirectResponse
    {
        $slug = $slug ?? $localeOrSlug;

        $book = Book::where('slug', $slug)->firstOrFail();

        if (! $book->isPublished()) {
            abort(404);
        }

        app(AnalyticsService::class)->captureFromRequest($request, AnalyticsEvent::BOOK_READ, [
            'book_id' => $book->id,
        ]);

        return redirect()->route('books.show', ['slug' => $book->slug]);
    }

    /**
     * Download action capturing book_download analytics.
     */
    public function download(Request $request, string $localeOrSlug, ?string $slug = null): BinaryFileResponse|RedirectResponse
    {
        $slug = $slug ?? $localeOrSlug;

        $book = Book::where('slug', $slug)->with('media')->firstOrFail();

        if (! $book->isPublished()) {
            abort(404);
        }

        $book->incrementQuietly('downloads_count');

        app(AnalyticsService::class)->captureFromRequest($request, AnalyticsEvent::BOOK_DOWNLOAD, [
            'book_id' => $book->id,
        ]);

        $pdfMedia = $book->media->where('collection_name', 'pdf')->first()
            ?? $book->media->filter(fn ($m) => str_contains($m->mime_type ?? '', 'pdf'))->first();

        if ($pdfMedia && file_exists(storage_path('app/'.$pdfMedia->file_path))) {
            return response()->download(storage_path('app/'.$pdfMedia->file_path), $pdfMedia->file_name);
        }

        return redirect()->route('books.show', ['slug' => $book->slug])
            ->with('status', 'Download initiated.');
    }
}
