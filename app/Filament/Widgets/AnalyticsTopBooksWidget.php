<?php

namespace App\Filament\Widgets;

use App\Enums\BookStatus;
use App\Models\AnalyticsEvent;
use App\Models\Book;
use App\Support\Analytics\AnalyticsEvent as EventCatalog;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class AnalyticsTopBooksWidget extends Widget
{
    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.analytics-top-books-widget';

    protected int|string|array $columnSpan = 'full';

    public string $rankingType = 'most_viewed';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('Super Admin') || $user->can('analytics.view'));
    }

    public function setRankingType(string $type): void
    {
        $this->rankingType = $type;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function getBooks(): Collection
    {
        return match ($this->rankingType) {
            'most_read' => $this->getMostRead(),
            'most_downloaded' => $this->getMostDownloaded(),
            'most_favorited' => $this->getMostFavorited(),
            'most_rated' => $this->getMostRated(),
            'highest_rated' => $this->getHighestRated(),
            'trending' => $this->getTrending(),
            default => $this->getMostViewed(),
        };
    }

    protected function getMostViewed(): Collection
    {
        return Book::where('status', BookStatus::Published)
            ->with(['authors.translations', 'translations'])
            ->orderByDesc('view_count')
            ->take(10)
            ->get()
            ->map(fn (Book $book) => [
                'id' => $book->id,
                'title' => $book->localized()?->title ?? $book->slug,
                'authors' => $book->authors->map(fn ($a) => $a->localized()?->name ?? $a->slug)->join(', ') ?: 'Unknown',
                'metric_label' => 'Total Views',
                'metric_value' => number_format($book->view_count),
                'rating' => $book->rating_average > 0 ? "{$book->rating_average} ★ ({$book->rating_count})" : 'No ratings',
            ]);
    }

    protected function getMostRead(): Collection
    {
        $topIds = AnalyticsEvent::where('event_name', EventCatalog::BOOK_READ)
            ->whereNotNull('book_id')
            ->selectRaw('book_id, count(*) as reads_count')
            ->groupBy('book_id')
            ->orderByDesc('reads_count')
            ->take(10)
            ->pluck('reads_count', 'book_id');

        if ($topIds->isEmpty()) {
            return collect();
        }

        $books = Book::whereIn('id', $topIds->keys())
            ->where('status', BookStatus::Published)
            ->with(['authors.translations', 'translations'])
            ->get()
            ->keyBy('id');

        return $topIds->map(function ($reads, $bookId) use ($books) {
            $book = $books->get($bookId);
            if (! $book) {
                return null;
            }

            return [
                'id' => $book->id,
                'title' => $book->localized()?->title ?? $book->slug,
                'authors' => $book->authors->map(fn ($a) => $a->localized()?->name ?? $a->slug)->join(', ') ?: 'Unknown',
                'metric_label' => 'Reads',
                'metric_value' => number_format($reads),
                'rating' => $book->rating_average > 0 ? "{$book->rating_average} ★ ({$book->rating_count})" : 'No ratings',
            ];
        })->filter()->values();
    }

    protected function getMostDownloaded(): Collection
    {
        return Book::where('status', BookStatus::Published)
            ->with(['authors.translations', 'translations'])
            ->orderByDesc('downloads_count')
            ->take(10)
            ->get()
            ->map(fn (Book $book) => [
                'id' => $book->id,
                'title' => $book->localized()?->title ?? $book->slug,
                'authors' => $book->authors->map(fn ($a) => $a->localized()?->name ?? $a->slug)->join(', ') ?: 'Unknown',
                'metric_label' => 'Downloads',
                'metric_value' => number_format($book->downloads_count),
                'rating' => $book->rating_average > 0 ? "{$book->rating_average} ★ ({$book->rating_count})" : 'No ratings',
            ]);
    }

    protected function getMostFavorited(): Collection
    {
        return Book::where('status', BookStatus::Published)
            ->with(['authors.translations', 'translations'])
            ->withCount('favorites')
            ->orderByDesc('favorites_count')
            ->take(10)
            ->get()
            ->map(fn (Book $book) => [
                'id' => $book->id,
                'title' => $book->localized()?->title ?? $book->slug,
                'authors' => $book->authors->map(fn ($a) => $a->localized()?->name ?? $a->slug)->join(', ') ?: 'Unknown',
                'metric_label' => 'Favorites',
                'metric_value' => number_format($book->favorites_count),
                'rating' => $book->rating_average > 0 ? "{$book->rating_average} ★ ({$book->rating_count})" : 'No ratings',
            ]);
    }

    protected function getMostRated(): Collection
    {
        return Book::where('status', BookStatus::Published)
            ->where('rating_count', '>', 0)
            ->with(['authors.translations', 'translations'])
            ->orderByDesc('rating_count')
            ->orderByDesc('rating_average')
            ->take(10)
            ->get()
            ->map(fn (Book $book) => [
                'id' => $book->id,
                'title' => $book->localized()?->title ?? $book->slug,
                'authors' => $book->authors->map(fn ($a) => $a->localized()?->name ?? $a->slug)->join(', ') ?: 'Unknown',
                'metric_label' => 'Total Ratings',
                'metric_value' => number_format($book->rating_count),
                'rating' => "{$book->rating_average} ★ ({$book->rating_count})",
            ]);
    }

    protected function getHighestRated(): Collection
    {
        // Enforce sensible minimum threshold of >= 3 ratings to prevent 1-review skew
        return Book::where('status', BookStatus::Published)
            ->where('rating_count', '>=', 3)
            ->with(['authors.translations', 'translations'])
            ->orderByDesc('rating_average')
            ->orderByDesc('rating_count')
            ->take(10)
            ->get()
            ->map(fn (Book $book) => [
                'id' => $book->id,
                'title' => $book->localized()?->title ?? $book->slug,
                'authors' => $book->authors->map(fn ($a) => $a->localized()?->name ?? $a->slug)->join(', ') ?: 'Unknown',
                'metric_label' => 'Average Rating',
                'metric_value' => number_format($book->rating_average, 2).' ★',
                'rating' => "{$book->rating_average} ★ ({$book->rating_count} ratings)",
            ]);
    }

    protected function getTrending(): Collection
    {
        $recentStart = Carbon::today()->subDays(6)->startOfDay();
        $recentEnd = Carbon::today()->endOfDay();

        $recentViews = AnalyticsEvent::where('event_name', EventCatalog::BOOK_VIEW)
            ->whereBetween('occurred_at', [$recentStart, $recentEnd])
            ->whereNotNull('book_id')
            ->selectRaw('book_id, count(*) as recent_views')
            ->groupBy('book_id')
            ->orderByDesc('recent_views')
            ->take(10)
            ->pluck('recent_views', 'book_id');

        if ($recentViews->isEmpty()) {
            return $this->getMostViewed();
        }

        $books = Book::whereIn('id', $recentViews->keys())
            ->where('status', BookStatus::Published)
            ->with(['authors.translations', 'translations'])
            ->get()
            ->keyBy('id');

        return $recentViews->map(function ($views, $bookId) use ($books) {
            $book = $books->get($bookId);
            if (! $book) {
                return null;
            }

            return [
                'id' => $book->id,
                'title' => $book->localized()?->title ?? $book->slug,
                'authors' => $book->authors->map(fn ($a) => $a->localized()?->name ?? $a->slug)->join(', ') ?: 'Unknown',
                'metric_label' => '7-Day Views',
                'metric_value' => '+'.number_format($views),
                'rating' => $book->rating_average > 0 ? "{$book->rating_average} ★ ({$book->rating_count})" : 'No ratings',
            ];
        })->filter()->values();
    }
}
