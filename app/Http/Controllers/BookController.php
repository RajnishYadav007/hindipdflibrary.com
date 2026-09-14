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
use Illuminate\Support\Facades\Storage;
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

        // If book is external source only, redirect safely to official source
        if ($book->hasExternalSourceOnly()) {
            $safeUrl = $book->getSafeSourceUrl();
            if ($safeUrl) {
                app(AnalyticsService::class)->captureFromRequest($request, AnalyticsEvent::BOOK_READ, [
                    'book_id' => $book->id,
                ]);

                return redirect()->away($safeUrl);
            }
        }

        if (! $book->canDownloadPdf()) {
            abort(403, 'Reading is not permitted for this title.');
        }

        app(AnalyticsService::class)->captureFromRequest($request, AnalyticsEvent::BOOK_READ, [
            'book_id' => $book->id,
        ]);

        return redirect()->route('books.show', ['slug' => $book->slug]);
    }

    /**
     * Download action enforcing legal rights and streaming private PDFs.
     */
    public function download(Request $request, string $localeOrSlug, ?string $slug = null): BinaryFileResponse|RedirectResponse
    {
        $slug = $slug ?? $localeOrSlug;

        $book = Book::where('slug', $slug)->with('media')->firstOrFail();

        if (! $book->isPublished()) {
            abort(404);
        }

        // Strict legal & content-rights check
        if (! $book->canDownloadPdf()) {
            abort(403, 'Direct file download is not permitted for this title.');
        }

        // Locate PDF media record or direct file on private_pdfs disk
        $pdfMedia = $book->media->where('collection_name', 'pdf')->first()
            ?? $book->media->filter(fn ($m) => str_contains($m->mime_type ?? '', 'pdf'))->first();

        $resolvedPath = null;
        if ($pdfMedia) {
            $disk = $pdfMedia->disk ?: 'private_pdfs';
            $relative = $pdfMedia->file_path ?: $pdfMedia->file_name;
            if (Storage::disk($disk)->exists($relative)) {
                $resolvedPath = Storage::disk($disk)->path($relative);
            } elseif (file_exists(storage_path('app/private_pdfs/'.$pdfMedia->file_name))) {
                $resolvedPath = storage_path('app/private_pdfs/'.$pdfMedia->file_name);
            } elseif (! empty($pdfMedia->file_path) && file_exists(storage_path('app/'.$pdfMedia->file_path))) {
                $resolvedPath = storage_path('app/'.$pdfMedia->file_path);
            }
        } elseif (Storage::disk('private_pdfs')->exists("books/{$book->slug}.pdf")) {
            $resolvedPath = Storage::disk('private_pdfs')->path("books/{$book->slug}.pdf");
        } elseif (Storage::disk('private_pdfs')->exists("{$book->slug}.pdf")) {
            $resolvedPath = Storage::disk('private_pdfs')->path("{$book->slug}.pdf");
        } elseif (! empty($book->pdf_path) && Storage::disk('private_pdfs')->exists($book->pdf_path)) {
            $resolvedPath = Storage::disk('private_pdfs')->path($book->pdf_path);
        }

        if (! $resolvedPath || ! file_exists($resolvedPath)) {
            abort(404, 'PDF file is not available on storage disk.');
        }

        // Verify magic bytes
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $resolvedPath);
        finfo_close($finfo);

        if ($mime !== 'application/pdf') {
            abort(415, 'Invalid digital media format.');
        }

        app(AnalyticsService::class)->captureFromRequest($request, AnalyticsEvent::BOOK_DOWNLOAD, [
            'book_id' => $book->id,
        ]);

        $downloadName = ($book->slug ?: 'book').'.pdf';

        return response()->file($resolvedPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.addslashes($downloadName).'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
