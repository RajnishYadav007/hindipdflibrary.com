<?php

namespace App\Console\Commands;

use App\Models\AnalyticsDaily;
use App\Models\AnalyticsEvent;
use App\Models\AnalyticsSearchDaily;
use Illuminate\Console\Command;
use Throwable;

class PruneAnalyticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune expired raw events and legacy aggregated records according to retention policies';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $rawRetentionDays = (int) config('analytics.retention.raw_events_days', 30);
        $dailyRetentionDays = (int) config('analytics.retention.daily_aggregates_days', 365);

        $rawCutoff = now()->subDays($rawRetentionDays);
        $dailyCutoff = now()->subDays($dailyRetentionDays)->format('Y-m-d');

        $this->info("Pruning raw analytics events older than {$rawRetentionDays} days (before {$rawCutoff->toDateTimeString()})...");

        try {
            $rawCount = AnalyticsEvent::where('occurred_at', '<', $rawCutoff)->delete();
            $dailyCount = AnalyticsDaily::where('date', '<', $dailyCutoff)->delete();
            $searchDailyCount = AnalyticsSearchDaily::where('date', '<', $dailyCutoff)->delete();

            $this->info("Pruning completed: {$rawCount} raw events deleted, {$dailyCount} daily aggregates deleted, {$searchDailyCount} search aggregates deleted.");

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Failed to prune analytics: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
