<?php

namespace App\Console\Commands;

use App\Models\AnalyticsDaily;
use App\Models\AnalyticsEvent;
use App\Models\AnalyticsSearchDaily;
use App\Support\Analytics\AnalyticsEvent as EventCatalog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class AggregateAnalyticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:aggregate {--date= : Target date in YYYY-MM-DD format (defaults to today)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate raw analytics events into daily intelligence tables';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dateInput = $this->option('date');
        $targetDate = $dateInput ? Carbon::parse($dateInput)->format('Y-m-d') : Carbon::today()->format('Y-m-d');

        $this->info("Starting analytics aggregation for date: {$targetDate}");

        $startOfDay = "{$targetDate} 00:00:00";
        $endOfDay = "{$targetDate} 23:59:59";

        DB::beginTransaction();
        try {
            // 1. Aggregate general analytics events
            $aggregates = AnalyticsEvent::whereBetween('occurred_at', [$startOfDay, $endOfDay])
                ->select([
                    'event_name',
                    'book_id',
                    DB::raw("COALESCE(source, 'other') as source"),
                    DB::raw("COALESCE(country, 'unknown') as country"),
                    DB::raw("COALESCE(device_type, 'unknown') as device_type"),
                    DB::raw('COUNT(*) as event_count'),
                    DB::raw('COUNT(DISTINCT visitor_id) as unique_visitors'),
                    DB::raw('COUNT(DISTINCT session_id) as unique_sessions'),
                ])
                ->groupBy(['event_name', 'book_id', 'source', 'country', 'device_type'])
                ->get();

            // Clear existing rows for this date to make aggregation idempotent
            AnalyticsDaily::where('date', $targetDate)->delete();

            $now = now();
            $dailyRows = [];
            foreach ($aggregates as $item) {
                $dailyRows[] = [
                    'date' => $targetDate,
                    'event_name' => $item->event_name,
                    'book_id' => $item->book_id,
                    'source' => $item->source,
                    'country' => $item->country,
                    'device_type' => $item->device_type,
                    'event_count' => $item->event_count,
                    'unique_visitors' => $item->unique_visitors,
                    'unique_sessions' => $item->unique_sessions,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (! empty($dailyRows)) {
                foreach (array_chunk($dailyRows, 250) as $chunk) {
                    AnalyticsDaily::insert($chunk);
                }
            }

            // 2. Aggregate Search Analytics
            $searchEvents = AnalyticsEvent::whereBetween('occurred_at', [$startOfDay, $endOfDay])
                ->whereIn('event_name', [EventCatalog::SEARCH, EventCatalog::SEARCH_RESULT_CLICK])
                ->whereNotNull('search_query')
                ->where('search_query', '!=', '')
                ->select([
                    DB::raw('LOWER(TRIM(search_query)) as normalized_query'),
                    DB::raw("SUM(CASE WHEN event_name = '".EventCatalog::SEARCH."' THEN 1 ELSE 0 END) as search_count"),
                    DB::raw("SUM(CASE WHEN event_name = '".EventCatalog::SEARCH."' AND (search_result_count = 0 OR search_result_count IS NULL) THEN 1 ELSE 0 END) as zero_result_count"),
                    DB::raw("SUM(CASE WHEN event_name = '".EventCatalog::SEARCH_RESULT_CLICK."' THEN 1 ELSE 0 END) as click_count"),
                ])
                ->groupBy('normalized_query')
                ->get();

            AnalyticsSearchDaily::where('date', $targetDate)->delete();

            $searchRows = [];
            foreach ($searchEvents as $search) {
                $searchRows[] = [
                    'date' => $targetDate,
                    'search_query' => $search->normalized_query,
                    'locale' => 'en',
                    'search_count' => (int) $search->search_count,
                    'zero_result_count' => (int) $search->zero_result_count,
                    'click_count' => (int) $search->click_count,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (! empty($searchRows)) {
                foreach (array_chunk($searchRows, 250) as $chunk) {
                    AnalyticsSearchDaily::insert($chunk);
                }
            }

            DB::commit();

            $this->info('Analytics aggregated successfully: '.count($dailyRows).' daily dimension rows, '.count($searchRows).' search term rows.');

            return Command::SUCCESS;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->error('Failed to aggregate analytics: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
