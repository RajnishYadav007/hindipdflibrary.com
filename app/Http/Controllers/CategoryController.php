<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\Analytics\AnalyticsService;
use App\Services\SEO\SeoManager;
use App\Support\Analytics\AnalyticsEvent;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
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
