<?php

namespace App\Http\Controllers;

use App\Models\Audiobook;
use App\Models\Language;
use App\Services\SEO\SeoManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AudiobookController extends Controller
{
    /**
     * Display a paginated listing of published audiobooks.
     */
    public function index(Request $request): View
    {
        $query = Audiobook::published()
            ->with([
                'translations',
                'language',
                'media',
                'publishedChapters',
            ]);

        if ($request->filled('language')) {
            $langSlug = $request->input('language');
            $query->whereHas('language', fn ($q) => $q->where('code', $langSlug));
        }

        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('narrator', 'like', "%{$term}%")
                    ->orWhereHas('translations', fn ($t) => $t->where('title', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%"));
            });
        }

        $audiobooks = $query->orderBy('sort_order', 'asc')
            ->orderBy('published_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $languages = Language::all();
        $locale = app()->getLocale();
        $seo = SeoManager::forAudiobookIndex($locale);

        return view('public.audiobooks.index', compact('audiobooks', 'languages', 'seo'));
    }

    /**
     * Display the specified audiobook with its chapter tracklist and audio player.
     * ZERO external network requests are made during this request.
     */
    public function show(Request $request, string $localeOrSlug, ?string $slug = null): View
    {
        $actualSlug = $slug ?? $localeOrSlug;
        $locale = in_array($localeOrSlug, ['hi', 'en'], true) ? $localeOrSlug : app()->getLocale();

        $audiobook = Audiobook::where('slug', $actualSlug)
            ->with([
                'translations',
                'language',
                'book.translations',
                'media',
                'publishedChapters' => fn ($q) => $q->with(['translations', 'media'])->orderBy('chapter_number', 'asc'),
            ])
            ->firstOrFail();

        // Check public visibility or authorization for preview
        if (! $audiobook->isPublished()) {
            $user = $request->user();
            if (! ($user && $user->is_admin)) {
                abort(404);
            }
        }

        // Increment play count
        $audiobook->increment('play_count');

        $chapters = $audiobook->publishedChapters;
        $seo = SeoManager::forAudiobook($audiobook, $locale);

        return view('public.audiobooks.show', compact('audiobook', 'chapters', 'seo'));
    }
}
