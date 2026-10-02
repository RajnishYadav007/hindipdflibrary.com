<?php

namespace App\Filament\Resources\Audiobooks\Pages;

use App\Filament\Resources\Audiobooks\AudiobookResource;
use App\Models\Audiobook;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateAudiobook extends CreateRecord
{
    protected static string $resource = AudiobookResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $coverImage = $data['cover_image'] ?? null;
        $audioFile = $data['audio_file'] ?? null;
        unset($data['cover_image'], $data['audio_file']);

        /** @var Audiobook $audiobook */
        $audiobook = static::getModel()::create($data);

        if ($coverImage) {
            $size = 0;
            $mimeType = null;
            try {
                if (Storage::disk('public')->exists($coverImage)) {
                    $size = (int) Storage::disk('public')->size($coverImage);
                    $mimeType = Storage::disk('public')->mimeType($coverImage);
                }
            } catch (\Throwable) {
            }

            $audiobook->media()->create([
                'uuid' => (string) Str::uuid(),
                'collection_name' => 'cover',
                'name' => 'Cover Image',
                'file_name' => $coverImage,
                'mime_type' => $mimeType,
                'size' => $size,
                'disk' => 'public',
            ]);
        }

        if ($audioFile) {
            $size = 0;
            $mimeType = null;
            try {
                if (Storage::disk('public')->exists($audioFile)) {
                    $size = (int) Storage::disk('public')->size($audioFile);
                    $mimeType = Storage::disk('public')->mimeType($audioFile);
                }
            } catch (\Throwable) {
            }

            $audiobook->media()->create([
                'uuid' => (string) Str::uuid(),
                'collection_name' => 'audio',
                'name' => 'Audio File',
                'file_name' => $audioFile,
                'mime_type' => $mimeType,
                'size' => $size,
                'disk' => 'public',
            ]);
        }

        return $audiobook;
    }
}
