<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Language;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display the search results page with faceted filters.
     */
    public function index(Request $request): View
    {
        $query = trim($request->string('q')->toString());
        $categoryId = $request->filled('category') ? $request->integer('category') : null;
        $genreId = $request->filled('genre') ? $request->integer('genre') : null;
        $languageId = $request->filled('language') ? $request->integer('language') : null;
        $sort = $request->string('sort', 'newest')->toString();

        $booksQuery = Book::published()
            ->with(['translations', 'authors.translations', 'categories.translations', 'media']);

        if ($query !== '') {
            $booksQuery->where(function ($q) use ($query) {
                $q->whereHas('translations', function ($sub) use ($query) {
                    $sub->where('title', 'like', "%{$query}%");
                })
                    ->orWhereHas('authors.translations', function ($sub) use ($query) {
                        $sub->where('name', 'like', "%{$query}%");
                    })
                    ->orWhere('isbn', 'like', "%{$query}%")
                    ->orWhere('isbn13', 'like', "%{$query}%");
            });
        }

        if ($categoryId) {
            $booksQuery->whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId));
        }

        if ($genreId) {
            $booksQuery->whereHas('genres', fn ($q) => $q->where('genres.id', $genreId));
        }

        if ($languageId) {
            $booksQuery->where(function ($q) use ($languageId) {
                $q->whereHas('languages', fn ($sub) => $sub->where('languages.id', $languageId))
                    ->orWhere('original_language_id', $languageId);
            });
        }

        match ($sort) {
            'popular' => $booksQuery->orderByDesc('view_count'),
            'rating' => $booksQuery->orderByDesc('rating_average')->orderByDesc('rating_count'),
            'title' => $booksQuery->orderBy('slug'),
            'oldest' => $booksQuery->oldest('id'),
            default => $booksQuery->latest('id'),
        };

        $books = $booksQuery->paginate(12)->withQueryString();

        $categories = Category::active()->with('translations')->orderBy('slug')->get();
        $genres = Genre::active()->with('translations')->orderBy('slug')->get();
        $languages = Language::where('is_active', true)->orderBy('name')->get();

        return view('public.search.index', compact(
            'books',
            'query',
            'categoryId',
            'genreId',
            'languageId',
            'sort',
            'categories',
            'genres',
            'languages'
        ));
    }
}
