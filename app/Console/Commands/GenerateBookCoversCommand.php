<?php

namespace App\Console\Commands;

use App\Filament\Resources\Books\Concerns\HandlesBookMedia;
use App\Models\Book;
use App\Services\Media\BookCoverService;
use Illuminate\Console\Command;

class GenerateBookCoversCommand extends Command
{
    use HandlesBookMedia;

    protected $signature = 'books:generate-covers {--slug= : Generate cover for a specific book slug} {--force : Regenerate cover even if one exists}';

    protected $description = 'Automatically extract or fetch book covers from PDF first page or Archive.org';

    public function handle(BookCoverService $coverService): int
    {
        $slug = $this->option('slug');
        $force = $this->option('force');

        $query = Book::query();
        if ($slug) {
            $query->where('slug', $slug);
        }

        $books = $query->with('media')->get();
        $this->info("Scanning {$books->count()} books for cover generation...");

        $generated = 0;
        foreach ($books as $book) {
            $hasCover = $book->media()->where('collection_name', 'cover')->exists();
            if ($hasCover && ! $force) {
                $this->line("  [Skip] '{$book->title}' already has a cover image.");

                continue;
            }

            $this->line("  [Processing] Generating cover for '{$book->title}'...");
            $pdfMedia = $book->media()->where('collection_name', 'pdf')->first();
            $coverPath = $coverService->autoGenerateCover($book, $pdfMedia?->file_name);

            if ($coverPath) {
                $this->syncCoverMedia($book, $coverPath);
                $this->info("    ✓ Cover generated successfully: {$coverPath}");
                $generated++;
            } else {
                $this->warn("    ✗ Could not generate cover for '{$book->title}'");
            }
        }

        $this->info("Finished! Generated covers for {$generated} book(s).");

        return Command::SUCCESS;
    }
}
