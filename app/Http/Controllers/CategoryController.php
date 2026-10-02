<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Genre;
use App\Services\Analytics\AnalyticsService;
use App\Services\SEO\SeoManager;
use App\Support\Analytics\AnalyticsEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of subject categories with their subcategories and book counts.
     */
    public function index(Request $request): View
    {
        $categories = Category::active()
            ->whereNull('parent_id')
            ->with([
                'translations',
                'children' => fn ($q) => $q->active()->with(['translations'])->withCount(['books' => fn ($b) => $b->published()]),
            ])
            ->withCount(['books' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        $isHindi = app()->getLocale() === 'hi';
        $title = $isHindi
            ? 'विषय श्रेणियाँ — हिन्दी पीडीएफ पुस्तकालय (Subject Categories)'
            : 'Subject Categories — Hindi PDF Library';

        return view('public.categories.index', compact('categories', 'title'));
    }

    /**
     * Display a listing of literary genres and forms.
     */
    public function genres(Request $request): View
    {
        $genres = Genre::active()
            ->with(['translations'])
            ->withCount(['books' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        $isHindi = app()->getLocale() === 'hi';
        $title = $isHindi
            ? 'साहित्यिक विधाएँ — हिन्दी पीडीएफ पुस्तकालय (Genres & Forms)'
            : 'Genres & Literary Forms — Hindi PDF Library';

        return view('public.categories.genres', compact('genres', 'title'));
    }

    /**
     * Display the specified category page.
     */
    public function show(string $localeOrSlug, ?string $slug = null): View
    {
        $slug = $slug ?? $localeOrSlug;

        $category = Category::where('slug', $slug)
            ->with(['translations', 'parent.translations'])
            ->firstOrFail();

        if (! $category->is_active) {
            abort(404);
        }

        // Capture category_view analytics event safely
        app(AnalyticsService::class)->captureFromRequest(request(), AnalyticsEvent::CATEGORY_VIEW, [
            'category_id' => $category->id,
        ]);

        $subcategories = $category->children()
            ->active()
            ->with(['translations'])
            ->withCount(['books' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        $books = $category->books()
            ->published()
            ->with(['translations', 'authors.translations', 'categories.translations', 'media'])
            ->latest('id')
            ->paginate(12);

        $seo = SeoManager::forCategory($category, app()->getLocale());

        return view('public.categories.show', compact('category', 'subcategories', 'books', 'seo'));
    }
}
