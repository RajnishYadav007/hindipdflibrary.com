<?php

namespace App\Filament\Resources\Stories\Pages;

use App\Filament\Resources\Stories\StoryEpisodeResource;
use App\Models\StoryEpisode;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateStoryEpisode extends CreateRecord
{
    protected static string $resource = StoryEpisodeResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $audioFile = $data['audio_file'] ?? null;
        unset($data['audio_file']);

        /** @var StoryEpisode $episode */
        $episode = static::getModel()::create($data);

        if ($audioFile) {
            $episode->media()->create([
                'uuid' => (string) Str::uuid(),
                'collection_name' => 'audio',
                'name' => 'Episode Audio Narration',
                'file_name' => $audioFile,
                'disk' => 'public',
            ]);
        }

        return $episode;
    }
}
