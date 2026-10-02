<?php

namespace App\Jobs;

use App\Models\Audiobook;
use App\Services\Archive\ArchiveOrgService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncArchiveAudiobookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 180;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Audiobook $audiobook,
        public string $archiveIdentifier
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ArchiveOrgService $archiveService): void
    {
        Log::info("SyncArchiveAudiobookJob: Starting sync for audiobook '{$this->audiobook->slug}' from Archive.org item '{$this->archiveIdentifier}'");

        try {
            $syncedCount = $archiveService->syncAudiobookFromArchive($this->audiobook, $this->archiveIdentifier);
            Log::info("SyncArchiveAudiobookJob: Successfully synced {$syncedCount} chapters for audiobook '{$this->audiobook->slug}'");
        } catch (\Throwable $e) {
            Log::error("SyncArchiveAudiobookJob: Failed syncing audiobook '{$this->audiobook->slug}': ".$e->getMessage(), [
                'exception' => $e,
            ]);
            throw $e;
        }
    }
}
