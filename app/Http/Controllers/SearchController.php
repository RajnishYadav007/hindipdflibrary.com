<?php

namespace App\Http\Controllers;

use App\Contracts\Search\SearchEngineInterface;
use App\Models\Audiobook;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\DevotionalContent;
use App\Models\Genre;
use App\Models\Language;
use App\Services\Analytics\AnalyticsService;
use App\Services\Discovery\BookDiscoveryService;
use App\Services\SEO\SeoManager;
use App\Support\Analytics\AnalyticsEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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

        // Capture search event when a query is provided
        if ($query !== '') {
            app(AnalyticsService::class)->captureFromRequest($request, AnalyticsEvent::SEARCH, [
                'search_query' => $query,
                'search_result_count' => $books->total(),
            ]);
        }

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

    /**
     * Track user click on a book from search results and redirect to the book page.
     */
    public function click(Request $request): RedirectResponse
    {
        $bookId = $request->integer('book_id');
        $query = trim($request->string('q')->toString());

        $book = Book::where('id', $bookId)->firstOrFail();

        app(AnalyticsService::class)->captureFromRequest($request, AnalyticsEvent::SEARCH_RESULT_CLICK, [
            'book_id' => $book->id,
            'search_query' => $query ?: null,
        ]);

        return redirect()->route('books.show', ['slug' => $book->slug]);
    }

    /**
     * Return JSON suggestions for live autocomplete (books, authors, categories).
     */
    public function suggest(Request $request): JsonResponse
    {
        $query = trim($request->string('q')->toString());
        if (mb_strlen($query) < 2) {
            return response()->json([
                'books' => [],
                'authors' => [],
                'categories' => [],
            ]);
        }

        $locale = app()->getLocale();

        // 1. Search up to 4 books
        $books = Book::published()
            ->where(function ($q) use ($query) {
                $q->where('slug', 'like', "%{$query}%")
                    ->orWhereHas('translations', fn ($t) => $t->where('title', 'like', "%{$query}%"))
                    ->orWhereHas('authors.translations', fn ($t) => $t->where('name', 'like', "%{$query}%"));
            })
            ->with(['translations', 'authors.translations', 'media'])
            ->take(4)
            ->get()
            ->map(function ($book) use ($locale) {
                $primaryAuthor = $book->authors->first();

                return [
                    'id' => $book->id,
                    'title' => $book->localized($locale)?->title ?? $book->slug,
                    'author' => $primaryAuthor?->localized($locale)?->name ?? $primaryAuthor?->slug,
                    'cover_url' => $book->cover_url,
                    'url' => route('books.show', ['slug' => $book->slug]),
                ];
            });

        // 2. Search up to 3 authors
        $authors = Author::active()
            ->where(function ($q) use ($query) {
                $q->where('slug', 'like', "%{$query}%")
                    ->orWhereHas('translations', fn ($t) => $t->where('name', 'like', "%{$query}%"));
            })
            ->with(['translations'])
            ->withCount(['books' => fn ($q) => $q->published()])
            ->take(3)
            ->get()
            ->map(function ($author) use ($locale) {
                return [
                    'id' => $author->id,
                    'name' => $author->localized($locale)?->name ?? $author->slug,
                    'books_count' => $author->books_count,
                    'url' => route('authors.show', ['slug' => $author->slug]),
                ];
            });

        // 3. Search up to 3 categories
        $categories = Category::active()
            ->where(function ($q) use ($query) {
                $q->where('slug', 'like', "%{$query}%")
                    ->orWhereHas('translations', fn ($t) => $t->where('name', 'like', "%{$query}%"));
            })
            ->with(['translations'])
            ->withCount(['books' => fn ($q) => $q->published()])
            ->take(3)
            ->get()
            ->map(function ($category) use ($locale) {
                return [
                    'id' => $category->id,
                    'name' => $category->localized($locale)?->name ?? $category->slug,
                    'books_count' => $category->books_count,
                    'url' => route('categories.show', ['slug' => $category->slug]),
                ];
            });

        // 4. Search Audiobooks
        $audiobooks = Audiobook::published()
            ->where(function ($q) use ($query) {
                $q->where('narrator', 'like', "%{$query}%")
                    ->orWhereHas('translations', fn ($t) => $t->where('title', 'like', "%{$query}%"));
            })
            ->with(['translations', 'media'])
            ->take(3)
            ->get()
            ->map(fn ($ab) => [
                'id' => $ab->id,
                'title' => $ab->title,
                'type' => 'ऑडियोबुक',
                'url' => route('audiobooks.show', ['slug' => $ab->slug]),
            ]);

        // 5. Search Devotionals (Chalisa & Aarti)
        $devotionals = DevotionalContent::published()
            ->where(function ($q) use ($query) {
                $q->where('deity_name', 'like', "%{$query}%")
                    ->orWhere('author_name', 'like', "%{$query}%")
                    ->orWhereHas('translations', fn ($t) => $t->where('title', 'like', "%{$query}%"));
            })
            ->with(['translations'])
            ->take(3)
            ->get()
            ->map(fn ($dev) => [
                'id' => $dev->id,
                'title' => $dev->title,
                'type' => $dev->type === 'chalisa' ? 'चालीसा' : 'आरती',
                'url' => $dev->type === 'chalisa' ? route('chalisa.show', ['slug' => $dev->slug]) : route('aarti.show', ['slug' => $dev->slug]),
            ]);

        return response()->json([
            'books' => $books,
            'authors' => $authors,
            'categories' => $categories,
            'audiobooks' => $audiobooks,
            'devotionals' => $devotionals,
        ]);
    }
}
