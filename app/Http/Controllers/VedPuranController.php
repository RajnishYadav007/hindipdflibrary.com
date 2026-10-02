<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\SEO\SeoManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class VedPuranController extends Controller
{
    /**
     * Display the curated Vedas, Puranas & Devotional Chalisas directory hub.
     */
    public function index(Request $request): View
    {
        $locale = app()->getLocale();

        // 1. Fetch Books categorized under Vedas, Puranas, or Devotional Chalisa/Stotra
        $books = Book::published()
            ->with([
                'translations',
                'authors.translations',
                'categories.translations',
                'media',
            ])
            ->whereHas('categories', function ($q) {
                $q->whereIn('slug', [
                    'vedas-puranas',
                    'vedas',
                    'puranas',
                    'chalisa-stotra',
                    'chalisa',
                    'stotra',
                    'aarti',
                ]);
            })
            ->orderByDesc('view_count')
            ->get();

        // Categorize for quick section rendering
        $vedas = $books->filter(function ($book) {
            return $book->categories->contains(fn ($cat) => in_array($cat->slug, ['vedas', 'ved']) || str_contains(strtolower($book->slug), 'veda') || str_contains(strtolower($book->slug), 'ved'));
        });

        $puranas = $books->filter(function ($book) {
            return $book->categories->contains(fn ($cat) => in_array($cat->slug, ['puranas', 'puran']) || str_contains(strtolower($book->slug), 'puran'));
        });

        $chalisas = $books->filter(function ($book) {
            return $book->categories->contains(fn ($cat) => in_array($cat->slug, ['chalisa-stotra', 'chalisa', 'stotra', 'aarti']) || str_contains(strtolower($book->slug), 'chalisa'));
        });

        // Generate high-authority SEO metadata
        $seo = SeoManager::forVedPuran($locale);

        return view('public.ved-puran.index', compact(
            'books',
            'vedas',
            'puranas',
            'chalisas',
            'seo'
        ));
    }
}
