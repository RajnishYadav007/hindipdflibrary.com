<?php

namespace App\Filament\Resources\Stories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class StorySeriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Story Series Details')
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(150)
                                    ->unique(ignoreRecord: true),

                                Select::make('age_group')
                                    ->label('Age Bracket (आयु वर्ग)')
                                    ->options([
                                        '3-5' => '3 - 5 वर्ष (Toddlers)',
                                        '6-8' => '6 - 8 वर्ष (Early Readers)',
                                        '9-12' => '9 - 12 वर्ष (Young Readers)',
                                        'all' => 'सभी उम्र (All Ages)',
                                    ])
                                    ->default('6-8')
                                    ->required(),

                                Select::make('category_id')
                                    ->label('Category (श्रेणी)')
                                    ->relationship('category', 'slug')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                Select::make('language_id')
                                    ->label('Language')
                                    ->relationship('language', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft (अप्रकाशित)',
                                        'pending_review' => 'Pending Review (समीक्षाधीन)',
                                        'published' => 'Published (प्रकाशित)',
                                        'archived' => 'Archived (पुरालेखित)',
                                    ])
                                    ->default('draft')
                                    ->required(),
                            ]),

                        Tab::make('Translations (शीर्षक व विवरण)')
                            ->icon('heroicon-m-language')
                            ->schema([
                                Repeater::make('translations')
                                    ->relationship()
                                    ->schema([
                                        Select::make('locale')
                                            ->options([
                                                'hi' => 'हिन्दी (Hindi)',
                                                'en' => 'English',
                                            ])
                                            ->required(),

                                        TextInput::make('title')
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make('description')
                                            ->rows(3),

                                        TextInput::make('seo_title')
                                            ->maxLength(255),

                                        Textarea::make('seo_description')
                                            ->rows(2),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1),
                            ]),

                        Tab::make('Cover Image')
                            ->icon('heroicon-m-photo')
                            ->schema([
                                FileUpload::make('cover_image')
                                    ->label('Series Cover Image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('covers/stories'),
                            ]),

                        Tab::make('Moderation & Notes')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Select::make('submitted_by')
                                    ->relationship('submitter', 'name')
                                    ->disabled(),

                                Textarea::make('admin_notes')
                                    ->label('Admin Moderation Notes')
                                    ->rows(3),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
