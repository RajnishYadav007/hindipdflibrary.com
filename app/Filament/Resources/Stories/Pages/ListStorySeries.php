<?php

namespace App\Filament\Resources\Stories\Pages;

use App\Filament\Resources\Stories\StorySeriesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStorySeries extends ListRecords
{
    protected static string $resource = StorySeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
