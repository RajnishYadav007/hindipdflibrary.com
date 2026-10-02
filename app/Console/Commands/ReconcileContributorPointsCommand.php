<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Contributor\ContributorRewardService;
use Illuminate\Console\Command;

class ReconcileContributorPointsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contributor:reconcile-points {--user= : Reconcile only a specific user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile contributor library points and level caches against the append-only ledger';

    /**
     * Execute the console command.
     */
    public function handle(ContributorRewardService $rewardService): int
    {
        $userId = $this->option('user');

        if ($userId) {
            $user = User::find($userId);
            if (! $user) {
                $this->error("User #{$userId} not found.");

                return self::FAILURE;
            }

            $result = $rewardService->reconcileUserPoints($user);
            $this->info("Reconciled User #{$user->id} ({$user->name}): {$result['total_points']} pts, Level: {$result['contributor_level']}, Approved: {$result['approved_count']}");

            return self::SUCCESS;
        }

        $this->info('Starting reconciliation of all users...');
        $count = 0;

        User::chunk(100, function ($users) use ($rewardService, &$count) {
            foreach ($users as $user) {
                $rewardService->reconcileUserPoints($user);
                $count++;
            }
        });

        $this->info("Reconciliation complete. Successfully reconciled {$count} users.");

        return self::SUCCESS;
    }
}
