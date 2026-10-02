<?php

namespace App\Filament\Resources\Stories\Pages;

use App\Filament\Resources\Stories\StorySeriesResource;
use App\Models\StorySeries;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EditStorySeries extends EditRecord
{
    protected static string $resource = StorySeriesResource::class;

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
        /** @var StorySeries $record */
        $record = $this->record;

        $cover = $record->media()->where('collection_name', 'cover')->first();
        if ($cover) {
            $data['cover_image'] = $cover->file_name;
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $coverImage = $data['cover_image'] ?? null;
        unset($data['cover_image']);

        $record->update($data);

        /** @var StorySeries $record */
        if ($coverImage) {
            $existing = $record->media()->where('collection_name', 'cover')->first();
            if ($existing && $existing->file_name !== $coverImage) {
                $existing->delete();
                $existing = null;
            }
            if (! $existing) {
                $record->media()->create([
                    'uuid' => (string) Str::uuid(),
                    'collection_name' => 'cover',
                    'name' => 'Cover Image',
                    'file_name' => $coverImage,
                    'disk' => 'public',
                ]);
            }
        }

        return $record;
    }
}
