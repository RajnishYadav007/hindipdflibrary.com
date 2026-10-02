<?php

namespace App\Filament\Resources\Devotionals\Pages;

use App\Filament\Resources\Devotionals\AartiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAarti extends CreateRecord
{
    protected static string $resource = AartiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = 'aarti';

        return $data;
    }
}
