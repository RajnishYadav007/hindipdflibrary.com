<?php

namespace App\Filament\Resources\Devotionals\Pages;

use App\Filament\Resources\Devotionals\ChalisaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateChalisa extends CreateRecord
{
    protected static string $resource = ChalisaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'chalisa';

        return $data;
    }
}
