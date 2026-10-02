<?php

namespace App\Filament\Resources\Authors\Pages;

use App\Filament\Resources\Authors\AuthorResource;
use App\Filament\Resources\Authors\Concerns\HandlesAuthorMedia;
use App\Models\Author;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAuthor extends CreateRecord
{
    use HandlesAuthorMedia;

    protected static string $resource = AuthorResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $portrait = $data['portrait_image'] ?? null;
        unset($data['portrait_image']);

        /** @var Author $author */
        $author = static::getModel()::create($data);

        $this->syncAuthorMedia($author, $portrait);

        return $author;
    }
}
