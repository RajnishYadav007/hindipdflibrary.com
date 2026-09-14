<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsDaily;
use App\Models\AnalyticsEvent;
use App\Support\Analytics\AnalyticsEvent as EventCatalog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class AnalyticsTrafficChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Traffic Trends';

    public ?string $filter = '30d';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('Super Admin') || $user->can('analytics.view'));
    }

    protected function getFilters(): ?array
    {
        return [
            '7d' => 'Last 7 Days',
            '30d' => 'Last 30 Days',
            '90d' => 'Last 90 Days',
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $days = match ($this->filter) {
            '7d' => 7,
            '90d' => 90,
            default => 30,
        };

        $startDate = Carbon::today()->subDays($days - 1)->format('Y-m-d');
        $endDate = Carbon::today()->format('Y-m-d');

        // Fetch aggregated daily data
        $dailyData = AnalyticsDaily::where('event_name', EventCatalog::PAGE_VIEW)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, SUM(event_count) as total_views, SUM(unique_visitors) as total_visitors')
            ->groupBy('date')
            ->get()
            ->keyBy(fn ($item) => Carbon::parse($item->date)->format('Y-m-d'));

        // Incorporate today's real-time events if today is in range
        $todayStr = Carbon::today()->format('Y-m-d');
        $todayViews = AnalyticsEvent::whereBetween('occurred_at', ["{$todayStr} 00:00:00", "{$todayStr} 23:59:59"])
            ->where('event_name', EventCatalog::PAGE_VIEW)
            ->count();
        $todayVisitors = AnalyticsEvent::whereBetween('occurred_at', ["{$todayStr} 00:00:00", "{$todayStr} 23:59:59"])
            ->whereNotNull('visitor_id')
            ->distinct('visitor_id')
            ->count('visitor_id');

        $labels = [];
        $viewsData = [];
        $visitorsData = [];

        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $labels[] = $date->format('M d');

            if ($formattedDate === $todayStr) {
                // Prefer live raw counts for today
                $viewsData[] = $todayViews;
                $visitorsData[] = $todayVisitors;
            } elseif (isset($dailyData[$formattedDate])) {
                $viewsData[] = (int) $dailyData[$formattedDate]->total_views;
                $visitorsData[] = (int) $dailyData[$formattedDate]->total_visitors;
            } else {
                $viewsData[] = 0;
                $visitorsData[] = 0;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Page Views',
                    'data' => $viewsData,
                    'borderColor' => '#d97706',
                    'backgroundColor' => 'rgba(217, 119, 6, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Unique Visitors',
                    'data' => $visitorsData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
