<?php

namespace App\Filament\Resources\Audiobooks\Pages;

use App\Filament\Resources\Audiobooks\AudiobookResource;
use App\Models\Audiobook;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditAudiobook extends EditRecord
{
    protected static string $resource = AudiobookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Audiobook $record */
        $record = $this->record;

        $cover = $record->media()->where('collection_name', 'cover')->first();
        if ($cover) {
            $data['cover_image'] = $cover->file_name;
        }

        $audio = $record->media()->where('collection_name', 'audio')->first();
        if ($audio) {
            $data['audio_file'] = $audio->file_name;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $coverImage = $data['cover_image'] ?? null;
        $audioFile = $data['audio_file'] ?? null;
        unset($data['cover_image'], $data['audio_file']);

        $record->update($data);

        /** @var Audiobook $record */
        if ($coverImage) {
            $existing = $record->media()->where('collection_name', 'cover')->first();
            if ($existing && $existing->file_name !== $coverImage) {
                $existing->delete();
                $existing = null;
            }
            if (! $existing) {
                $size = 0;
                $mimeType = null;
                try {
                    if (Storage::disk('public')->exists($coverImage)) {
                        $size = (int) Storage::disk('public')->size($coverImage);
                        $mimeType = Storage::disk('public')->mimeType($coverImage);
                    }
                } catch (\Throwable) {
                }

                $record->media()->create([
                    'uuid' => (string) Str::uuid(),
                    'collection_name' => 'cover',
                    'name' => 'Cover Image',
                    'file_name' => $coverImage,
                    'mime_type' => $mimeType,
                    'size' => $size,
                    'disk' => 'public',
                ]);
            }
        }

        if ($audioFile) {
            $existing = $record->media()->where('collection_name', 'audio')->first();
            if ($existing && $existing->file_name !== $audioFile) {
                $existing->delete();
                $existing = null;
            }
            if (! $existing) {
                $size = 0;
                $mimeType = null;
                try {
                    if (Storage::disk('public')->exists($audioFile)) {
                        $size = (int) Storage::disk('public')->size($audioFile);
                        $mimeType = Storage::disk('public')->mimeType($audioFile);
                    }
                } catch (\Throwable) {
                }

                $record->media()->create([
                    'uuid' => (string) Str::uuid(),
                    'collection_name' => 'audio',
                    'name' => 'Audio File',
                    'file_name' => $audioFile,
                    'mime_type' => $mimeType,
                    'size' => $size,
                    'disk' => 'public',
                ]);
            }
        }

        return $record;
    }
}
