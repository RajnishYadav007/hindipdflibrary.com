<?php

namespace App\Http\Controllers;

use App\Models\StoryEpisode;
use App\Models\StorySeries;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    /**
     * Display a listing of children's story series with age group filters.
     */
    public function index(Request $request): View
    {
        $query = StorySeries::published()
            ->with([
                'translations',
                'category.translations',
                'language',
                'media',
                'publishedEpisodes',
            ]);

        if ($request->filled('age_group') && $request->input('age_group') !== 'all') {
            $query->forAge($request->input('age_group'));
        }

        if ($request->filled('search')) {
            $term = trim($request->input('search'));
            $query->whereHas('translations', function ($t) use ($term) {
                $t->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        $seriesList = $query->orderBy('sort_order', 'asc')
            ->orderBy('published_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $ageGroups = [
            'all' => 'सभी उम्र (All Ages)',
            '3-5' => '3 - 5 वर्ष (Toddlers)',
            '6-8' => '6 - 8 वर्ष (Early Readers)',
            '9-12' => '9 - 12 वर्ष (Young Readers)',
        ];

        return view('public.stories.index', compact('seriesList', 'ageGroups'));
    }

    /**
     * Display the specified story series with its episode list.
     */
    public function series(Request $request, string $localeOrSeriesSlug, ?string $seriesSlug = null): View
    {
        $seriesSlug = $seriesSlug ?? $localeOrSeriesSlug;

        $series = StorySeries::where('slug', $seriesSlug)
            ->with([
                'translations',
                'category.translations',
                'language',
                'media',
                'publishedEpisodes' => fn ($q) => $q->with(['translations', 'media'])->orderBy('episode_number', 'asc'),
            ])
            ->firstOrFail();

        if (! $series->isPublished()) {
            $user = $request->user();
            if (! ($user && $user->is_admin)) {
                abort(404);
            }
        }

        $series->increment('view_count');

        $episodes = $series->publishedEpisodes;

        return view('public.stories.series', compact('series', 'episodes'));
    }

    /**
     * Read an individual episode with dual audio experience (Narration audio or browser TTS).
     */
    public function episode(Request $request, string $localeOrSeriesSlug, ?string $seriesOrEpisodeSlug = null, ?string $episodeSlug = null): View
    {
        if ($episodeSlug !== null) {
            $seriesSlug = $seriesOrEpisodeSlug;
        } else {
            $seriesSlug = $localeOrSeriesSlug;
            $episodeSlug = $seriesOrEpisodeSlug;
        }

        $series = StorySeries::where('slug', $seriesSlug)
            ->with(['translations', 'media'])
            ->firstOrFail();

        $episode = StoryEpisode::where('story_series_id', $series->id)
            ->where(function ($q) use ($episodeSlug) {
                $q->whereHas('translations', fn ($t) => $t->where('slug', $episodeSlug))
                    ->orWhere('episode_number', is_numeric($episodeSlug) ? (int) $episodeSlug : -1);
            })
            ->with(['translations', 'media', 'series'])
            ->firstOrFail();

        if (! $episode->isPublished() || ! $series->isPublished()) {
            $user = $request->user();
            if (! ($user && $user->is_admin)) {
                abort(404);
            }
        }

        $episode->increment('view_count');

        // Next & Prev episode navigation
        $prevEpisode = StoryEpisode::where('story_series_id', $series->id)
            ->published()
            ->where('episode_number', '<', $episode->episode_number)
            ->orderBy('episode_number', 'desc')
            ->first();

        $nextEpisode = StoryEpisode::where('story_series_id', $series->id)
            ->published()
            ->where('episode_number', '>', $episode->episode_number)
            ->orderBy('episode_number', 'asc')
            ->first();

        return view('public.stories.episode', compact('series', 'episode', 'prevEpisode', 'nextEpisode'));
    }
}
