<?php

namespace App\Filament\Resources\Stories\Pages;

use App\Filament\Resources\Stories\StorySeriesResource;
use App\Models\StorySeries;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateStorySeries extends CreateRecord
{
    protected static string $resource = StorySeriesResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $coverImage = $data['cover_image'] ?? null;
        unset($data['cover_image']);

        /** @var StorySeries $series */
        $series = static::getModel()::create($data);

        if ($coverImage) {
            $series->media()->create([
                'uuid' => (string) Str::uuid(),
                'collection_name' => 'cover',
                'name' => 'Cover Image',
                'file_name' => $coverImage,
                'disk' => 'public',
            ]);
        }

        return $series;
    }
}
