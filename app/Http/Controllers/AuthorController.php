<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Services\SEO\SeoManager;
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
