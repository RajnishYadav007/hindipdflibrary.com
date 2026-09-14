<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsEvent;
use App\Models\AnalyticsSearchDaily;
use App\Support\Analytics\AnalyticsEvent as EventCatalog;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsSearchWidget extends Widget
{
    protected static ?int $sort = 6;

    protected string $view = 'filament.widgets.analytics-search-widget';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('Super Admin') || $user->can('analytics.view'));
    }

    /**
     * @return array{total_searches: int, zero_result_searches: int, total_clicks: int, overall_ctr: float}
     */
    public function getOverview(): array
    {
        $thirtyDaysAgo = Carbon::today()->subDays(30)->format('Y-m-d');

        $dailyStats = AnalyticsSearchDaily::where('date', '>=', $thirtyDaysAgo)
            ->selectRaw('SUM(search_count) as total_searches, SUM(zero_result_count) as zero_results, SUM(click_count) as total_clicks')
            ->first();

        // Also add today's live search events
        $todayStr = Carbon::today()->format('Y-m-d');
        $todaySearches = AnalyticsEvent::whereBetween('occurred_at', ["{$todayStr} 00:00:00", "{$todayStr} 23:59:59"])
            ->where('event_name', EventCatalog::SEARCH)
            ->count();
        $todayZeroResults = AnalyticsEvent::whereBetween('occurred_at', ["{$todayStr} 00:00:00", "{$todayStr} 23:59:59"])
            ->where('event_name', EventCatalog::SEARCH)
            ->where(fn ($q) => $q->whereNull('search_result_count')->orWhere('search_result_count', 0))
            ->count();
        $todayClicks = AnalyticsEvent::whereBetween('occurred_at', ["{$todayStr} 00:00:00", "{$todayStr} 23:59:59"])
            ->where('event_name', EventCatalog::SEARCH_RESULT_CLICK)
            ->count();

        $totalSearches = (int) ($dailyStats?->total_searches ?? 0) + $todaySearches;
        $zeroResults = (int) ($dailyStats?->zero_results ?? 0) + $todayZeroResults;
        $totalClicks = (int) ($dailyStats?->total_clicks ?? 0) + $todayClicks;
        $overallCtr = $totalSearches > 0 ? round(($totalClicks / $totalSearches) * 100, 1) : 0.0;

        return [
            'total_searches' => $totalSearches,
            'zero_result_searches' => $zeroResults,
            'total_clicks' => $totalClicks,
            'overall_ctr' => $overallCtr,
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function getPopularSearches(): Collection
    {
        $thirtyDaysAgo = Carbon::today()->subDays(30)->format('Y-m-d');

        return AnalyticsSearchDaily::where('date', '>=', $thirtyDaysAgo)
            ->select([
                'search_query',
                DB::raw('SUM(search_count) as total_searches'),
                DB::raw('SUM(click_count) as total_clicks'),
            ])
            ->groupBy('search_query')
            ->orderByDesc('total_searches')
            ->take(8)
            ->get()
            ->map(function ($row) {
                $searches = (int) $row->total_searches;
                $clicks = (int) $row->total_clicks;
                $ctr = $searches > 0 ? round(($clicks / $searches) * 100, 1) : 0;

                return [
                    'query' => $row->search_query,
                    'count' => $searches,
                    'clicks' => $clicks,
                    'ctr' => "{$ctr}%",
                ];
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function getZeroResultSearches(): Collection
    {
        $thirtyDaysAgo = Carbon::today()->subDays(30)->format('Y-m-d');

        return AnalyticsSearchDaily::where('date', '>=', $thirtyDaysAgo)
            ->where('zero_result_count', '>', 0)
            ->select([
                'search_query',
                DB::raw('SUM(zero_result_count) as total_zero'),
            ])
            ->groupBy('search_query')
            ->orderByDesc('total_zero')
            ->take(8)
            ->get()
            ->map(fn ($row) => [
                'query' => $row->search_query,
                'count' => (int) $row->total_zero,
            ]);
    }
}
