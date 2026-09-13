<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Enums\BookStatus;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Book Details')
                    ->tabs([
                        Tab::make('Basic Information')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(150)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Unique, canonical URL identifier. Do not alter after publishing.'),

                                Select::make('status')
                                    ->options([
                                        BookStatus::Draft->value => 'Draft',
                                        BookStatus::Published->value => 'Published',
                                        BookStatus::Archived->value => 'Archived',
                                    ])
                                    ->default(BookStatus::Draft->value)
                                    ->required(),

                                Select::make('original_language_id')
                                    ->label('Original Language')
                                    ->relationship('originalLanguage', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select original language'),
                            ])
                            ->columns(3),

                        Tab::make('Classification')
                            ->icon('heroicon-m-tag')
                            ->schema([
                                Select::make('authors')
                                    ->label('Authors / Contributors')
                                    ->relationship('authors', 'slug')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->helperText('Select one or more authors or translators.'),

                                Select::make('categories')
                                    ->relationship('categories', 'slug')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),

                                Select::make('genres')
                                    ->relationship('genres', 'slug')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),

                                Select::make('languages')
                                    ->label('Available Language Editions')
                                    ->relationship('languages', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                            ])
                            ->columns(2),

                        Tab::make('Publication Details')
                            ->icon('heroicon-m-calendar')
                            ->schema([
                                TextInput::make('publisher')
                                    ->maxLength(150),

                                TextInput::make('publication_year')
                                    ->numeric()
                                    ->minValue(1000)
                                    ->maxValue(date('Y') + 1),

                                TextInput::make('edition')
                                    ->maxLength(50)
                                    ->placeholder('e.g. First Edition, Centenary Edition'),

                                TextInput::make('pages')
                                    ->numeric()
                                    ->minValue(1),

                                TextInput::make('isbn')
                                    ->label('ISBN-10')
                                    ->maxLength(20),

                                TextInput::make('isbn13')
                                    ->label('ISBN-13')
                                    ->maxLength(20),
                            ])
                            ->columns(3),

                        Tab::make('Multilingual Content')
                            ->icon('heroicon-m-language')
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

                                        TextInput::make('title')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('short_description')
                                            ->maxLength(500)
                                            ->columnSpanFull(),

                                        Textarea::make('description')
                                            ->rows(4)
                                            ->columnSpanFull(),

                                        Textarea::make('summary')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        TagsInput::make('key_themes')
                                            ->placeholder('Add theme and press Enter')
                                            ->columnSpanFull(),

                                        Textarea::make('why_read')
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

                        Tab::make('Digital File / Legal Source')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                TextInput::make('source_url')
                                    ->label('Archive / Source URL')
                                    ->url()
                                    ->maxLength(500)
                                    ->columnSpanFull(),

                                TextInput::make('license_type')
                                    ->placeholder('e.g. Public Domain, CC-BY-SA 4.0')
                                    ->maxLength(100),

                                TextInput::make('rights_holder')
                                    ->placeholder('e.g. Public Domain, Government of India')
                                    ->maxLength(255),

                                TextInput::make('copyright_status')
                                    ->placeholder('e.g. public_domain, copyrighted')
                                    ->maxLength(50),

                                Textarea::make('permission_notes')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
