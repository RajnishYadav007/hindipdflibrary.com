<?php

namespace App\Filament\Resources\Seo\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeoMetaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Technical SEO & Crawler Directives')
                    ->schema([
                        TextInput::make('seoable_type')
                            ->label('Entity Model Class')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('App\Models\Book'),

                        TextInput::make('seoable_id')
                            ->label('Entity ID')
                            ->required()
                            ->numeric(),

                        Select::make('locale')
                            ->options([
                                'hi' => 'Hindi (हिन्दी)',
                                'en' => 'English',
                                'sa' => 'Sanskrit (संस्कृतम्)',
                            ])
                            ->default('en')
                            ->required(),

                        Select::make('robots')
                            ->options([
                                'index, follow' => 'index, follow (Standard Indexing)',
                                'noindex, follow' => 'noindex, follow (Hide Page, Follow Links)',
                                'index, nofollow' => 'index, nofollow (Index Page, Do Not Follow)',
                                'noindex, nofollow' => 'noindex, nofollow (Block Crawler Completely)',
                            ])
                            ->default('index, follow')
                            ->required(),

                        TextInput::make('canonical_url')
                            ->url()
                            ->maxLength(500)
                            ->placeholder('https://hindipdflibrary.com/...')
                            ->columnSpanFull(),

                        TextInput::make('og_image')
                            ->label('OpenGraph Social Preview Image URL')
                            ->url()
                            ->maxLength(500)
                            ->columnSpanFull(),

                        TextInput::make('schema_type')
                            ->label('Schema.org Structured Type')
                            ->placeholder('Book, Person, CollectionPage')
                            ->maxLength(50),
                    ])
                    ->columns(2),
            ]);
    }
}
