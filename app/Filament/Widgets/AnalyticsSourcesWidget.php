<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsDaily;
use App\Models\AnalyticsEvent;
use App\Support\Analytics\AnalyticsEvent as EventCatalog;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class AnalyticsSourcesWidget extends Widget
{
    protected static ?int $sort = 5;

    protected string $view = 'filament.widgets.analytics-sources-widget';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('Super Admin') || $user->can('analytics.view'));
    }

    /**
     * @return array{sources: array<string, int>, devices: array<string, int>, countries: array<string, int>}
     */
    public function getBreakdown(): array
    {
        $thirtyDaysAgo = Carbon::today()->subDays(30)->format('Y-m-d');

        // Traffic sources
        $sources = AnalyticsDaily::where('event_name', EventCatalog::PAGE_VIEW)
            ->where('date', '>=', $thirtyDaysAgo)
            ->selectRaw('source, SUM(event_count) as total')
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray();

        // Also add today's events
        $todayStr = Carbon::today()->format('Y-m-d');
        $todaySources = AnalyticsEvent::whereBetween('occurred_at', ["{$todayStr} 00:00:00", "{$todayStr} 23:59:59"])
            ->where('event_name', EventCatalog::PAGE_VIEW)
            ->selectRaw("COALESCE(source, 'other') as source, count(*) as total")
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray();

        foreach ($todaySources as $src => $count) {
            $sources[$src] = ($sources[$src] ?? 0) + $count;
        }

        // Devices
        $devices = AnalyticsDaily::where('event_name', EventCatalog::PAGE_VIEW)
            ->where('date', '>=', $thirtyDaysAgo)
            ->selectRaw('device_type, SUM(event_count) as total')
            ->groupBy('device_type')
            ->pluck('total', 'device_type')
            ->toArray();

        $todayDevices = AnalyticsEvent::whereBetween('occurred_at', ["{$todayStr} 00:00:00", "{$todayStr} 23:59:59"])
            ->where('event_name', EventCatalog::PAGE_VIEW)
            ->selectRaw("COALESCE(device_type, 'unknown') as device_type, count(*) as total")
            ->groupBy('device_type')
            ->pluck('total', 'device_type')
            ->toArray();

        foreach ($todayDevices as $dev => $count) {
            $devices[$dev] = ($devices[$dev] ?? 0) + $count;
        }

        // Countries
        $countries = AnalyticsDaily::where('event_name', EventCatalog::PAGE_VIEW)
            ->where('date', '>=', $thirtyDaysAgo)
            ->where('country', '!=', 'unknown')
            ->selectRaw('country, SUM(event_count) as total')
            ->groupBy('country')
            ->orderByDesc('total')
            ->take(5)
            ->pluck('total', 'country')
            ->toArray();

        $todayCountries = AnalyticsEvent::whereBetween('occurred_at', ["{$todayStr} 00:00:00", "{$todayStr} 23:59:59"])
            ->where('event_name', EventCatalog::PAGE_VIEW)
            ->whereNotNull('country')
            ->where('country', '!=', 'unknown')
            ->selectRaw('country, count(*) as total')
            ->groupBy('country')
            ->pluck('total', 'country')
            ->toArray();

        foreach ($todayCountries as $c => $count) {
            $countries[$c] = ($countries[$c] ?? 0) + $count;
        }

        arsort($countries);

        return [
            'sources' => $sources,
            'devices' => $devices,
            'countries' => $countries,
        ];
    }
}
