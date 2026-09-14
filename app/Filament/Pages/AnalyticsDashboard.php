<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AnalyticsSearchWidget;
use App\Filament\Widgets\AnalyticsSourcesWidget;
use App\Filament\Widgets\AnalyticsTodayStatsWidget;
use App\Filament\Widgets\AnalyticsTopBooksWidget;
use App\Filament\Widgets\AnalyticsTrafficChartWidget;
use BackedEnum;
use Filament\Pages\Dashboard;

class AnalyticsDashboard extends Dashboard
{
    protected static string $routePath = '/analytics';

    protected static ?string $title = 'Analytics & Intelligence';

    protected static ?string $navigationLabel = 'Analytics';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('Super Admin') || $user->can('analytics.view'));
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            AnalyticsTodayStatsWidget::class,
            AnalyticsTrafficChartWidget::class,
            AnalyticsTopBooksWidget::class,
            AnalyticsSourcesWidget::class,
            AnalyticsSearchWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 1;
    }
}
