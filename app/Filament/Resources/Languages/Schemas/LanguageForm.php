<?php

namespace App\Filament\Resources\Languages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LanguageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Language Code')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true)
                    ->placeholder('e.g. hi, en, sa'),

                TextInput::make('name')
                    ->label('English Name')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('e.g. Hindi, English'),

                TextInput::make('native_name')
                    ->label('Native Script Name')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('e.g. हिन्दी, English'),

                Toggle::make('is_active')
                    ->label('Active in Library')
                    ->default(true),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ]);
    }
}
