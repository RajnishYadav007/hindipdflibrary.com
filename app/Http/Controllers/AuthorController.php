<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Services\Analytics\AnalyticsService;
use App\Services\SEO\SeoManager;
use App\Support\Analytics\AnalyticsEvent;
use Illuminate\Contracts\View\View;

class AuthorController extends Controller
{
    /**
     * Display the specified author profile page.
     */
    public function show(string $localeOrSlug, ?string $slug = null): View
    {
        $slug = $slug ?? $localeOrSlug;

        $author = Author::where('slug', $slug)
            ->with(['translations', 'media'])
            ->firstOrFail();

        if (! $author->is_active) {
            abort(404);
        }

        // Capture author_view analytics event safely
        app(AnalyticsService::class)->captureFromRequest(request(), AnalyticsEvent::AUTHOR_VIEW, [
            'author_id' => $author->id,
        ]);

        $books = $author->books()
            ->published()
            ->with(['translations', 'authors.translations', 'categories.translations', 'media'])
            ->latest('id')
            ->paginate(12);

        $relatedAuthors = Author::active()
            ->where('id', '!=', $author->id)
            ->with(['translations', 'media'])
            ->withCount(['books' => fn ($q) => $q->published()])
            ->orderByDesc('books_count')
            ->take(4)
            ->get();

        $seo = SeoManager::forAuthor($author, app()->getLocale());

        return view('public.authors.show', compact('author', 'books', 'relatedAuthors', 'seo'));
    }
}
