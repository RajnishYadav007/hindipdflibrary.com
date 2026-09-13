<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Media Asset Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('file_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('collection_name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('mime_type')
                            ->disabled(),

                        TextInput::make('disk')
                            ->disabled()
                            ->helperText('Storage disk boundary (public vs private_pdfs).'),

                        TextInput::make('size')
                            ->numeric()
                            ->disabled()
                            ->helperText('Size in bytes.'),
                    ])
                    ->columns(2),
            ]);
    }
}
