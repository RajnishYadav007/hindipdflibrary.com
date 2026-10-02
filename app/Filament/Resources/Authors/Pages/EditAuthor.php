<?php

namespace App\Filament\Resources\Authors\Pages;

use App\Filament\Resources\Authors\AuthorResource;
use App\Filament\Resources\Authors\Concerns\HandlesAuthorMedia;
use App\Models\Author;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAuthor extends EditRecord
{
    use HandlesAuthorMedia;

    protected static string $resource = AuthorResource::class;

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
        /** @var Author $author */
        $author = $this->record;

        return $this->populateAuthorMedia($author, $data);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $portrait = $data['portrait_image'] ?? null;
        unset($data['portrait_image']);

        $record->update($data);

        /** @var Author $author */
        $author = $record;

        $this->syncAuthorMedia($author, $portrait);

        return $author;
    }
}
