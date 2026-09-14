<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use App\Services\SEO\SeoManager;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Display the curated homepage.
     */
    public function __invoke(?string $locale = null): View
    {
        $activeLocale = app()->getLocale();

        $curatedData = Cache::remember("homepage_curated_{$activeLocale}", 1800, function () {
            $popularBooks = Book::published()
                ->with(['translations', 'authors.translations', 'categories.translations', 'media'])
                ->orderByDesc('view_count')
                ->take(8)
                ->get();

            $featuredBooks = Book::published()
                ->with(['translations', 'authors.translations', 'categories.translations', 'media'])
                ->orderByDesc('rating_average')
                ->orderByDesc('rating_count')
                ->take(8)
                ->get();

            $categories = Category::active()
                ->with(['translations'])
                ->withCount(['books' => fn ($q) => $q->published()])
                ->orderByDesc('books_count')
                ->take(6)
                ->get();

            $authors = Author::active()
                ->with(['translations', 'media'])
                ->withCount(['books' => fn ($q) => $q->published()])
                ->orderByDesc('books_count')
                ->take(6)
                ->get();

            $highlyRatedBooks = Book::published()
                ->where('rating_average', '>=', 4.0)
                ->with(['translations', 'authors.translations', 'categories.translations', 'media'])
                ->orderByDesc('rating_average')
                ->take(8)
                ->get();

            if ($highlyRatedBooks->isEmpty()) {
                $highlyRatedBooks = Book::published()
                    ->with(['translations', 'authors.translations', 'categories.translations', 'media'])
                    ->orderByDesc('rating_average')
                    ->take(8)
                    ->get();
            }

            $recentBooks = Book::published()
                ->with(['translations', 'authors.translations', 'categories.translations', 'media'])
                ->latest('id')
                ->take(8)
                ->get();

            $languages = Language::where('is_active', true)
                ->withCount(['books' => fn ($q) => $q->published()])
                ->orderByDesc('books_count')
                ->get();

            return compact(
                'popularBooks',
                'featuredBooks',
                'categories',
                'authors',
                'highlyRatedBooks',
                'recentBooks',
                'languages'
            );
        });

        $seo = SeoManager::forHome($activeLocale);

        return view('welcome', array_merge($curatedData, [
            'seo' => $seo,
        ]));
    }
}
