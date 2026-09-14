<?php

namespace App\Console\Commands;

use App\Services\Migration\WordPressMigrationService;
use Illuminate\Console\Command;
use Throwable;

class WordPressImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordpress:import 
                            {file : Path to WordPress export JSON or CSV file}
                            {--dry-run : Validate and simulate without saving changes to the database}
                            {--validate-only : Only run validation checks without persisting any data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import or validate legacy WordPress books with legal and permalink checks';

    /**
     * Execute the console command.
     */
    public function handle(WordPressMigrationService $migrationService): int
    {
        $filePath = (string) $this->argument('file');
        $dryRun = (bool) $this->option('dry-run');
        $validateOnly = (bool) $this->option('validate-only');

        if (! file_exists($filePath) || ! is_readable($filePath)) {
            $this->error("Export file not found or not readable at: {$filePath}");

            return Command::FAILURE;
        }

        $items = $this->parseFile($filePath);
        if ($items === null) {
            $this->error('Unable to parse file format. Expected valid JSON array or CSV.');

            return Command::FAILURE;
        }

        $this->info(sprintf(
            'Found %d items in %s. Mode: %s',
            count($items),
            basename($filePath),
            $validateOnly ? 'Validate Only' : ($dryRun ? 'Dry Run' : 'Live Import')
        ));

        $validCount = 0;
        $invalidCount = 0;
        $warningCount = 0;
        $importedCount = 0;
        $errorsReport = [];

        $progressBar = $this->output->createProgressBar(count($items));
        $progressBar->start();

        foreach ($items as $index => $rawItem) {
            $validation = $migrationService->validateBookData($rawItem);

            if (! $validation['valid']) {
                $invalidCount++;
                $errorsReport[] = [
                    'index' => $index + 1,
                    'title' => $rawItem['title'] ?? 'N/A',
                    'errors' => implode('; ', $validation['errors']),
                ];
            } else {
                $validCount++;

                if (! empty($validation['warnings'])) {
                    $warningCount += count($validation['warnings']);
                }

                if (! $validateOnly) {
                    try {
                        $book = $migrationService->importBook($validation['data'], $dryRun);
                        if ($book || $dryRun) {
                            $importedCount++;
                        }
                    } catch (Throwable $e) {
                        $invalidCount++;
                        $errorsReport[] = [
                            'index' => $index + 1,
                            'title' => $rawItem['title'] ?? 'N/A',
                            'errors' => 'Database error: '.$e->getMessage(),
                        ];
                    }
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary report
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Processed', count($items)],
                ['Valid Records', $validCount],
                ['Invalid Records', $invalidCount],
                ['Warnings Detected', $warningCount],
                ['Imported / Simulated', $importedCount],
            ]
        );

        if (! empty($errorsReport)) {
            $this->warn('Sample Validation / Import Errors (First 10):');
            $sampleErrors = array_slice($errorsReport, 0, 10);
            $this->table(['Item #', 'Title', 'Errors'], $sampleErrors);
        }

        if ($invalidCount > 0 && ! $dryRun && ! $validateOnly) {
            $this->warn("Completed with {$invalidCount} invalid records skipped.");
        } else {
            $this->info('WordPress migration task finished successfully.');
        }

        return Command::SUCCESS;
    }

    /**
     * Parse export file into an array of associative arrays.
     *
     * @return array<int, array<string, mixed>>|null
     */
    protected function parseFile(string $path): ?array
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === 'json') {
            $content = file_get_contents($path);
            $decoded = json_decode((string) $content, true);

            return is_array($decoded) ? $decoded : null;
        }

        if ($extension === 'csv') {
            $items = [];
            if (($handle = fopen($path, 'r')) !== false) {
                $headers = fgetcsv($handle);
                if (! $headers) {
                    fclose($handle);

                    return null;
                }

                $headers = array_map('trim', $headers);
                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row) === count($headers)) {
                        $items[] = array_combine($headers, $row);
                    }
                }
                fclose($handle);
            }

            return $items;
        }

        return null;
    }
}
