<?php

namespace App\Filament\Resources\Authors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuthorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Author Details')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(150)
                            ->unique(ignoreRecord: true),

                        Toggle::make('is_active')
                            ->default(true),

                        FileUpload::make('portrait_image')
                            ->label('Author Portrait (लेखक का चित्र)')
                            ->image()
                            ->disk('public')
                            ->directory('authors')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->columnSpanFull()
                            ->helperText('Upload author photo (JPG, PNG, WebP).'),
                    ])
                    ->columns(2),

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

                                Textarea::make('biography')
                                    ->rows(3)
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
