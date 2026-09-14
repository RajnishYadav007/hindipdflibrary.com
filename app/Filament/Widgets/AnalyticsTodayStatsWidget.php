<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsEvent;
use App\Models\User;
use App\Support\Analytics\AnalyticsEvent as EventCatalog;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsTodayStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('Super Admin') || $user->can('analytics.view'));
    }

    protected function getStats(): array
    {
        $today = Carbon::today()->format('Y-m-d');
        $startOfDay = "{$today} 00:00:00";
        $endOfDay = "{$today} 23:59:59";

        $eventCounts = AnalyticsEvent::whereBetween('occurred_at', [$startOfDay, $endOfDay])
            ->selectRaw('event_name, count(*) as total')
            ->groupBy('event_name')
            ->pluck('total', 'event_name')
            ->toArray();

        $pageViews = $eventCounts[EventCatalog::PAGE_VIEW] ?? 0;
        $booksRead = $eventCounts[EventCatalog::BOOK_READ] ?? 0;
        $downloads = $eventCounts[EventCatalog::BOOK_DOWNLOAD] ?? 0;
        $favorites = $eventCounts[EventCatalog::BOOK_FAVORITE] ?? 0;
        $ratings = $eventCounts[EventCatalog::RATING_SUBMITTED] ?? 0;
        $reviews = $eventCounts[EventCatalog::REVIEW_SUBMITTED] ?? 0;

        $uniqueVisitors = AnalyticsEvent::whereBetween('occurred_at', [$startOfDay, $endOfDay])
            ->whereNotNull('visitor_id')
            ->distinct('visitor_id')
            ->count('visitor_id');

        $uniqueSessions = AnalyticsEvent::whereBetween('occurred_at', [$startOfDay, $endOfDay])
            ->whereNotNull('session_id')
            ->distinct('session_id')
            ->count('session_id');

        $newUsers = User::whereBetween('created_at', [$startOfDay, $endOfDay])->count();

        return [
            Stat::make("Today's Views", number_format($pageViews))
                ->description('Total page views today')
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary'),

            Stat::make('Visitors', number_format($uniqueVisitors))
                ->description('Unique visitor cookies')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),

            Stat::make('Sessions', number_format($uniqueSessions))
                ->description('30m active windows')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make('New Users', number_format($newUsers))
                ->description('Registered today')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success'),

            Stat::make('Books Read', number_format($booksRead))
                ->description('Reader sessions opened')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),

            Stat::make('Downloads', number_format($downloads))
                ->description('PDF downloads initiated')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success'),

            Stat::make('Favorites', number_format($favorites))
                ->description('Books added to favorites')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger'),

            Stat::make('Ratings', number_format($ratings))
                ->description('Star ratings submitted')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Reviews', number_format($reviews))
                ->description('Textual reviews submitted')
                ->descriptionIcon('heroicon-m-chat-bubble-bottom-center-text')
                ->color('warning'),
        ];
    }
}
