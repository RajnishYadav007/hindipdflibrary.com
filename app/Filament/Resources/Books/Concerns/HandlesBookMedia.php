<?php

namespace App\Filament\Resources\Books\Concerns;

use App\Models\Book;
use App\Models\Media;
use App\Services\Media\BookCoverService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesBookMedia
{
    /**
     * Populate existing media paths into the form before filling.
     */
    protected function populateBookMedia(Book $book, array $data): array
    {
        $cover = $book->media()->where('collection_name', 'cover')->first();
        if (! $cover) {
            $pdf = $book->media()->where('collection_name', 'pdf')->first();
            if ($pdf || ! empty($book->source_url)) {
                $coverService = app(BookCoverService::class);
                $autoCover = $coverService->autoGenerateCover($book, $pdf?->file_name);
                if ($autoCover) {
                    $this->syncCoverMedia($book, $autoCover);
                    $cover = $book->media()->where('collection_name', 'cover')->first();
                }
            }
        }

        if ($cover) {
            $data['cover_image'] = $cover->file_name;
        }

        $pdf = $book->media()->where('collection_name', 'pdf')->first();
        if ($pdf) {
            $data['pdf_file'] = $pdf->file_name;
        }

        return $data;
    }

    /**
     * Synchronize cover image and PDF document media relations.
     */
    protected function syncBookMedia(Book $book, ?string $coverPath, ?string $pdfPath): void
    {
        $this->syncCoverMedia($book, $coverPath);
        $this->syncPdfMedia($book, $pdfPath);
    }

    /**
     * Sync cover image in public storage disk.
     */
    protected function syncCoverMedia(Book $book, ?string $coverPath): void
    {
        $existingCover = $book->media()->where('collection_name', 'cover')->first();

        if (empty($coverPath)) {
            if ($existingCover) {
                $existingCover->delete();
            }

            return;
        }

        if ($existingCover && $existingCover->file_name === $coverPath) {
            return;
        }

        $disk = 'public';
        $size = Storage::disk($disk)->exists($coverPath) ? Storage::disk($disk)->size($coverPath) : 0;
        $mime = Storage::disk($disk)->exists($coverPath) ? Storage::disk($disk)->mimeType($coverPath) : 'image/jpeg';

        if ($existingCover) {
            $existingCover->update([
                'file_name' => $coverPath,
                'name' => pathinfo($coverPath, PATHINFO_FILENAME),
                'mime_type' => $mime,
                'size' => $size,
                'disk' => $disk,
            ]);
        } else {
            Media::create([
                'model_type' => Book::class,
                'model_id' => $book->id,
                'uuid' => Str::uuid()->toString(),
                'collection_name' => 'cover',
                'name' => pathinfo($coverPath, PATHINFO_FILENAME),
                'file_name' => $coverPath,
                'mime_type' => $mime,
                'disk' => $disk,
                'size' => $size,
            ]);
        }
    }

    /**
     * Sync PDF document in private_pdfs storage disk.
     */
    protected function syncPdfMedia(Book $book, ?string $pdfPath): void
    {
        $existingPdf = $book->media()->where('collection_name', 'pdf')->first();

        if (empty($pdfPath)) {
            if ($existingPdf) {
                $existingPdf->delete();
            }

            return;
        }

        if ($existingPdf && $existingPdf->file_name === $pdfPath) {
            return;
        }

        $disk = 'private_pdfs';
        $size = Storage::disk($disk)->exists($pdfPath) ? Storage::disk($disk)->size($pdfPath) : 0;
        $mime = 'application/pdf';

        if ($existingPdf) {
            $existingPdf->update([
                'file_name' => $pdfPath,
                'name' => pathinfo($pdfPath, PATHINFO_FILENAME),
                'mime_type' => $mime,
                'size' => $size,
                'disk' => $disk,
            ]);
        } else {
            Media::create([
                'model_type' => Book::class,
                'model_id' => $book->id,
                'uuid' => Str::uuid()->toString(),
                'collection_name' => 'pdf',
                'name' => pathinfo($pdfPath, PATHINFO_FILENAME),
                'file_name' => $pdfPath,
                'mime_type' => $mime,
                'disk' => $disk,
                'size' => $size,
            ]);
        }
    }
}
