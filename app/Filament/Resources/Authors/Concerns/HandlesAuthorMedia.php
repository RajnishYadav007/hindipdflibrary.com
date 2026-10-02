<?php

namespace App\Filament\Resources\Authors\Concerns;

use App\Models\Author;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesAuthorMedia
{
    /**
     * Populate existing portrait media path before filling form.
     */
    protected function populateAuthorMedia(Author $author, array $data): array
    {
        $portrait = $author->media()->where('collection_name', 'portrait')->first();
        if ($portrait) {
            $data['portrait_image'] = $portrait->file_name;
        }

        return $data;
    }

    /**
     * Synchronize author portrait media relation.
     */
    protected function syncAuthorMedia(Author $author, ?string $portraitPath): void
    {
        $existing = $author->media()->where('collection_name', 'portrait')->first();

        if (empty($portraitPath)) {
            if ($existing) {
                $existing->delete();
            }

            return;
        }

        if ($existing && $existing->file_name === $portraitPath) {
            return;
        }

        $disk = 'public';
        $size = Storage::disk($disk)->exists($portraitPath) ? Storage::disk($disk)->size($portraitPath) : 0;
        $mime = Storage::disk($disk)->exists($portraitPath) ? Storage::disk($disk)->mimeType($portraitPath) : 'image/jpeg';

        if ($existing) {
            $existing->update([
                'file_name' => $portraitPath,
                'name' => pathinfo($portraitPath, PATHINFO_FILENAME),
                'mime_type' => $mime,
                'size' => $size,
                'disk' => $disk,
            ]);
        } else {
            Media::create([
                'model_type' => Author::class,
                'model_id' => $author->id,
                'uuid' => Str::uuid()->toString(),
                'collection_name' => 'portrait',
                'name' => pathinfo($portraitPath, PATHINFO_FILENAME),
                'file_name' => $portraitPath,
                'mime_type' => $mime,
                'disk' => $disk,
                'size' => $size,
            ]);
        }
    }
}
