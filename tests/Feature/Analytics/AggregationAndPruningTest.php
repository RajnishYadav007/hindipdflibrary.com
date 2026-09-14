<?php

namespace Tests\Feature\Analytics;

use App\Models\AnalyticsDaily;
use App\Models\AnalyticsEvent;
use App\Models\AnalyticsSearchDaily;
use App\Support\Analytics\AnalyticsEvent as EventCatalog;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AggregationAndPruningTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_aggregation_produces_correct_totals(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $now = Carbon::today()->addHours(10);

        // Seed raw page_view events
        AnalyticsEvent::create([
            'event_name' => EventCatalog::PAGE_VIEW,
            'visitor_id' => 'v1',
            'session_id' => 's1',
            'source' => 'google',
            'device_type' => 'desktop',
            'occurred_at' => $now,
        ]);
        AnalyticsEvent::create([
            'event_name' => EventCatalog::PAGE_VIEW,
            'visitor_id' => 'v1',
            'session_id' => 's1',
            'source' => 'google',
            'device_type' => 'desktop',
            'occurred_at' => $now->copy()->addMinutes(5),
        ]);
        AnalyticsEvent::create([
            'event_name' => EventCatalog::PAGE_VIEW,
            'visitor_id' => 'v2',
            'session_id' => 's2',
            'source' => 'direct',
            'device_type' => 'mobile',
            'occurred_at' => $now->copy()->addMinutes(10),
        ]);

        // Seed search events
        AnalyticsEvent::create([
            'event_name' => EventCatalog::SEARCH,
            'search_query' => 'Gita',
            'search_result_count' => 15,
            'occurred_at' => $now,
        ]);
        AnalyticsEvent::create([
            'event_name' => EventCatalog::SEARCH,
            'search_query' => 'Gita',
            'search_result_count' => 15,
            'occurred_at' => $now->copy()->addMinutes(2),
        ]);
        AnalyticsEvent::create([
            'event_name' => EventCatalog::SEARCH,
            'search_query' => 'MissingBook',
            'search_result_count' => 0,
            'occurred_at' => $now->copy()->addMinutes(3),
        ]);
        AnalyticsEvent::create([
            'event_name' => EventCatalog::SEARCH_RESULT_CLICK,
            'search_query' => 'Gita',
            'occurred_at' => $now->copy()->addMinutes(4),
        ]);

        // Run aggregation command
        $exitCode = Artisan::call('analytics:aggregate', ['--date' => $today]);
        $this->assertSame(0, $exitCode);

        // Verify general daily aggregation
        $googleAggregate = AnalyticsDaily::where('date', $today)
            ->where('source', 'google')
            ->first();

        $this->assertNotNull($googleAggregate);
        $this->assertSame(2, $googleAggregate->event_count);
        $this->assertSame(1, $googleAggregate->unique_visitors); // v1 only
        $this->assertSame(1, $googleAggregate->unique_sessions); // s1 only

        $directAggregate = AnalyticsDaily::where('date', $today)
            ->where('source', 'direct')
            ->first();

        $this->assertNotNull($directAggregate);
        $this->assertSame(1, $directAggregate->event_count);
        $this->assertSame(1, $directAggregate->unique_visitors);

        // Verify search aggregation
        $gitaSearch = AnalyticsSearchDaily::where('date', $today)
            ->where('search_query', 'gita')
            ->first();

        $this->assertNotNull($gitaSearch);
        $this->assertSame(2, $gitaSearch->search_count);
        $this->assertSame(0, $gitaSearch->zero_result_count);
        $this->assertSame(1, $gitaSearch->click_count);

        $missingSearch = AnalyticsSearchDaily::where('date', $today)
            ->where('search_query', 'missingbook')
            ->first();

        $this->assertNotNull($missingSearch);
        $this->assertSame(1, $missingSearch->search_count);
        $this->assertSame(1, $missingSearch->zero_result_count);
        $this->assertSame(0, $missingSearch->click_count);

        // Verify idempotency: running twice does not duplicate rows
        Artisan::call('analytics:aggregate', ['--date' => $today]);
        $this->assertSame(1, AnalyticsDaily::where('date', $today)->where('source', 'google')->count());
        $this->assertSame(1, AnalyticsSearchDaily::where('date', $today)->where('search_query', 'gita')->count());
    }

    public function test_pruning_removes_expired_records(): void
    {
        // Raw event older than 30 days
        $oldRaw = AnalyticsEvent::create([
            'event_name' => EventCatalog::PAGE_VIEW,
            'occurred_at' => now()->subDays(45),
        ]);

        // Recent raw event
        $recentRaw = AnalyticsEvent::create([
            'event_name' => EventCatalog::PAGE_VIEW,
            'occurred_at' => now()->subDays(5),
        ]);

        // Old daily aggregate older than 365 days
        $oldDaily = AnalyticsDaily::create([
            'date' => now()->subDays(400)->format('Y-m-d'),
            'event_name' => EventCatalog::PAGE_VIEW,
            'event_count' => 10,
        ]);

        // Recent daily aggregate
        $recentDaily = AnalyticsDaily::create([
            'date' => now()->subDays(10)->format('Y-m-d'),
            'event_name' => EventCatalog::PAGE_VIEW,
            'event_count' => 50,
        ]);

        $exitCode = Artisan::call('analytics:prune');
        $this->assertSame(0, $exitCode);

        // Old records pruned
        $this->assertDatabaseMissing('analytics_events', ['id' => $oldRaw->id]);
        $this->assertDatabaseMissing('analytics_daily', ['id' => $oldDaily->id]);

        // Recent records preserved
        $this->assertDatabaseHas('analytics_events', ['id' => $recentRaw->id]);
        $this->assertDatabaseHas('analytics_daily', ['id' => $recentDaily->id]);
    }
}
