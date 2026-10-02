<?php

namespace App\Console\Commands;

use App\Jobs\SyncArchiveAudiobookJob;
use App\Models\Audiobook;
use App\Services\Archive\ArchiveOrgService;
use Illuminate\Console\Command;

class SyncArchiveAudiobookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audiobook:sync-archive 
                            {audiobook : The slug or ID of the audiobook} 
                            {identifier? : The Archive.org item identifier (optional if already set on audiobook)}
                            {--queue : Dispatch as a background queued job}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest and synchronize chapters and verified metadata from Archive.org item';

    /**
     * Execute the console command.
     */
    public function handle(ArchiveOrgService $archiveService): int
    {
        $audiobookParam = $this->argument('audiobook');
        $audiobook = is_numeric($audiobookParam)
            ? Audiobook::find($audiobookParam)
            : Audiobook::where('slug', $audiobookParam)->first();

        if (! $audiobook) {
            $this->error("Audiobook not found for: {$audiobookParam}");

            return self::FAILURE;
        }

        $identifier = $this->argument('identifier');
        if (empty($identifier)) {
            $identifier = $archiveService->resolveItemIdentifier($audiobook->source_url)
                ?: $archiveService->resolveItemIdentifier($audiobook->external_audio_url);
        }

        if (empty($identifier)) {
            $this->error('No Archive.org identifier provided or discovered on audiobook.');

            return self::FAILURE;
        }

        if ($this->option('queue')) {
            SyncArchiveAudiobookJob::dispatch($audiobook, $identifier);
            $this->info("Dispatched SyncArchiveAudiobookJob for audiobook '{$audiobook->slug}' (Archive item: {$identifier})");

            return self::SUCCESS;
        }

        $this->info("Starting synchronous Archive.org synchronization for '{$audiobook->slug}' (Identifier: {$identifier})...");

        try {
            $syncedCount = $archiveService->syncAudiobookFromArchive($audiobook, $identifier);
            $this->info("Successfully synchronized {$syncedCount} chapters! Total duration: {$audiobook->formatted_duration}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Archive sync failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
