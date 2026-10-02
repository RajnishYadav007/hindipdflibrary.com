<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use App\Services\SEO\SeoManager;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Display the curated homepage.
     */
    public function __invoke(?string $locale = null): View
    {
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
            ->take(8)
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

        // Real Database Trust Statistics
        $stats = [
            'books' => Book::published()->count(),
            'authors' => Author::active()->count(),
            'categories' => Category::active()->count(),
            'languages' => Language::where('is_active', true)->count(),
        ];

        // Layered Books for Hero Composition (3 prominent books)
        $heroBooks = Book::published()
            ->whereIn('slug', ['bhagavad-gita', 'ramcharitmanas', 'godaan'])
            ->with(['translations', 'authors.translations', 'media'])
            ->get();

        if ($heroBooks->count() < 3) {
            $heroBooks = $featuredBooks->take(3);
        }

        // Dedicated Children Stories
        $childrenStories = Book::published()
            ->where(function ($q) {
                $q->whereHas('categories', fn ($cat) => $cat->where('slug', 'children-stories'))
                    ->orWhere('slug', 'like', '%kahani%')
                    ->orWhere('slug', 'like', '%panchatantra%')
                    ->orWhere('slug', 'like', '%akbar%')
                    ->orWhere('slug', 'like', '%tenali%');
            })
            ->with(['translations', 'authors.translations', 'media'])
            ->take(8)
            ->get();

        // Dedicated Audiobooks / Spoken Works with rich playback details
        $audioBooks = Book::published()
            ->where(function ($q) {
                $q->whereIn('content_type', ['chalisa', 'aarti', 'stotra', 'mantra'])
                    ->orWhereIn('slug', ['hanuman-chalisa', 'bhagavad-gita', 'shiv-chalisa', 'ramcharitmanas', 'godaan', 'bajrang-baan', 'hanuman-aarti']);
            })
            ->with(['translations', 'authors.translations', 'media'])
            ->take(6)
            ->get();

        $devotionalItems = Book::published()
            ->whereIn('content_type', ['chalisa', 'aarti', 'stotra', 'mantra'])
            ->with(['translations', 'authors.translations', 'media'])
            ->take(6)
            ->get();
        $valueBadges = [
            [
                'subtitle' => 'Public Domain',
                'title' => 'Free to Read',
                'description' => 'Digitized classics and public-domain works available without paywalls or sign-ups.',
            ],
            [
                'subtitle' => 'Multi-format',
                'title' => 'Text, Audio & PDF',
                'description' => 'Explore books in readable, listenable, and downloadable formats for modern access.',
            ],
            [
                'subtitle' => 'Curated',
                'title' => 'Heritage Collections',
                'description' => 'Carefully organized sections for classics, chalisas, stories, and spiritual literature.',
            ],
            [
                'subtitle' => 'Open Access',
                'title' => 'Built for Discovery',
                'description' => 'A clean catalog that helps readers browse, search, and reach the right book quickly.',
            ],
        ];

        $seo = SeoManager::forHome(app()->getLocale());

        return view('welcome', compact(
            'popularBooks',
            'featuredBooks',
            'categories',
            'authors',
            'highlyRatedBooks',
            'recentBooks',
            'languages',
            'stats',
            'heroBooks',
            'audioBooks',
            'devotionalItems',
            'childrenStories',
            'valueBadges',
            'seo'
        ));
    }
}
