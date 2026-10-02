<?php

namespace App\Filament\Resources\Audiobooks\Pages;

use App\Filament\Resources\Audiobooks\AudiobookResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAudiobooks extends ListRecords
{
    protected static string $resource = AudiobookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
