<?php

namespace App\Filament\Resources\Devotionals\Pages;

use App\Filament\Resources\Devotionals\AartiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAarti extends EditRecord
{
    protected static string $resource = AartiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
