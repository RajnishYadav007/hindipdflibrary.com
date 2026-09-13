<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    /**
     * Display the specified category page.
     */
    public function show(string $slug): View
    {
        $category = Category::where('slug', $slug)
            ->with(['translations', 'parent.translations'])
            ->firstOrFail();

        if (! $category->is_active) {
            abort(404);
        }

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

        return view('public.categories.show', compact('category', 'subcategories', 'books'));
    }
}
