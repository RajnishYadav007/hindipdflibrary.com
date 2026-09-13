<?php

namespace App\Filament\Resources\Genres\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GenreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(150)
                            ->unique(ignoreRecord: true),

                        Toggle::make('is_active')
                            ->default(true),

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),

                Section::make('Translations (Multilingual)')
                    ->schema([
                        Repeater::make('translations')
                            ->relationship()
                            ->schema([
                                Select::make('locale')
                                    ->options([
                                        'hi' => 'Hindi (हिन्दी)',
                                        'en' => 'English',
                                        'sa' => 'Sanskrit (संस्कृतम्)',
                                    ])
                                    ->required(),

                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(150),

                                Textarea::make('description')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                TextInput::make('seo_title')
                                    ->maxLength(255),

                                Textarea::make('seo_description')
                                    ->rows(2),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->collapsible(),
                    ]),
            ]);
    }
}
