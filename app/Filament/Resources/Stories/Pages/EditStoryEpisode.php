<?php

namespace App\Filament\Resources\Stories\Pages;

use App\Filament\Resources\Stories\StoryEpisodeResource;
use App\Models\StoryEpisode;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EditStoryEpisode extends EditRecord
{
    protected static string $resource = StoryEpisodeResource::class;

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
        /** @var StoryEpisode $record */
        $record = $this->record;

        $audio = $record->media()->where('collection_name', 'audio')->first();
        if ($audio) {
            $data['audio_file'] = $audio->file_name;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $audioFile = $data['audio_file'] ?? null;
        unset($data['audio_file']);

        $record->update($data);

        /** @var StoryEpisode $record */
        if ($audioFile) {
            $existing = $record->media()->where('collection_name', 'audio')->first();
            if ($existing && $existing->file_name !== $audioFile) {
                $existing->delete();
                $existing = null;
            }
            if (! $existing) {
                $record->media()->create([
                    'uuid' => (string) Str::uuid(),
                    'collection_name' => 'audio',
                    'name' => 'Episode Audio Narration',
                    'file_name' => $audioFile,
                    'disk' => 'public',
                ]);
            }
        }

        return $record;
    }
}
