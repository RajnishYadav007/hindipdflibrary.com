<?php

namespace App\Filament\Resources\Devotionals\Pages;

use App\Filament\Resources\Devotionals\ChalisaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChalisas extends ListRecords
{
    protected static string $resource = ChalisaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
