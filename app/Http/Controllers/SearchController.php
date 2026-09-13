<?php

namespace App\Http\Controllers;

use App\Contracts\Search\SearchEngineInterface;
use App\Models\Author;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Language;
use App\Services\Discovery\BookDiscoveryService;
use App\Services\SEO\SeoManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        protected SearchEngineInterface $searchEngine,
        protected BookDiscoveryService $discoveryService
    ) {}

    /**
     * Display the search results page with faceted filters and discovery suggestions.
     */
    public function index(Request $request): View
    {
        $query = trim($request->string('q')->toString());
        $categoryId = $request->filled('category') ? $request->integer('category') : null;
        $genreId = $request->filled('genre') ? $request->integer('genre') : null;
        $authorId = $request->filled('author') ? $request->integer('author') : null;
        $languageId = $request->filled('language') ? $request->integer('language') : null;
        $sort = $request->string('sort', 'newest')->toString();

        $filters = array_filter([
            'category' => $categoryId,
            'genre' => $genreId,
            'author' => $authorId,
            'language' => $languageId,
            'sort' => $sort,
        ]);

        $locale = app()->getLocale();

        // Perform search using the decoupled search engine contract
        $books = $this->searchEngine->search($query, $locale, $filters, 12);
        $books->appends($request->query());

        // Empty results discovery suggestions
        $suggestedBooks = $books->isEmpty()
            ? $this->discoveryService->popularBooks(4)
            : collect();

        // Faceted filter options (only active/published entities)
        $categories = Category::active()->with('translations')->orderBy('slug')->get();
        $genres = Genre::active()->with('translations')->orderBy('slug')->get();
        $authors = Author::active()->with('translations')->orderBy('slug')->get();
        $languages = Language::where('is_active', true)->orderBy('name')->get();

        // Phase 9 Semantic SEO with noindex, follow
        $seo = SeoManager::forSearch($locale, $query ?: null);

        return view('public.search.index', compact(
            'books',
            'suggestedBooks',
            'query',
            'categoryId',
            'genreId',
            'authorId',
            'languageId',
            'sort',
            'categories',
            'genres',
            'authors',
            'languages',
            'seo'
        ));
    }
}
