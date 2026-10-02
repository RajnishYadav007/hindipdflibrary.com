<?php

namespace App\Filament\Resources\Devotionals\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class DevotionalForm
{
    public static function configure(Schema $schema, string $defaultType = 'chalisa'): Schema
    {
        return $schema
            ->components([
                Tabs::make('Devotional Details')
                    ->tabs([
                        Tab::make('General Information')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Select::make('type')
                                    ->options([
                                        'chalisa' => '🚩 चालीसा (Chalisa)',
                                        'aarti' => '🪔 आरती (Aarti)',
                                    ])
                                    ->default($defaultType)
                                    ->required(),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(150)
                                    ->unique(ignoreRecord: true),

                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft (अप्रकाशित)',
                                        'pending_review' => 'Pending Review (समीक्षाधीन)',
                                        'published' => 'Published (प्रकाशित)',
                                        'archived' => 'Archived (पुरालेखित)',
                                    ])
                                    ->default('draft')
                                    ->required(),

                                TextInput::make('deity_name')
                                    ->label('Deity Name (इष्ट देवता / देवी)')
                                    ->placeholder('जैसे: हनुमान, शिव, राम, दुर्गा')
                                    ->required()
                                    ->maxLength(100),

                                TextInput::make('author_name')
                                    ->label('Author / Saint (रचयिता)')
                                    ->placeholder('जैसे: गोस्वामी तुलसीदास')
                                    ->maxLength(150),

                                Select::make('language_id')
                                    ->label('Language')
                                    ->relationship('language', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        Tab::make('Translations & Stanzas')
                            ->icon('heroicon-m-document-text')
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
                                            ->rows(2),

                                        RichEditor::make('content')
                                            ->label('Stanzas / Chaupais / Lyrics (सम्पूर्ण पाठ)')
                                            ->required()
                                            ->columnSpanFull(),

                                        TextInput::make('seo_title')
                                            ->maxLength(255),

                                        Textarea::make('seo_description')
                                            ->rows(2),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1),
                            ]),

                        Tab::make('Moderation & Audit')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Select::make('submitted_by')
                                    ->relationship('submitter', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(),

                                Textarea::make('admin_notes')
                                    ->label('Moderator Notes (समीक्षा टिप्पणी)')
                                    ->rows(3)
                                    ->helperText('उपयोगकर्ता को दिखने वाली टिप्पणी अथवा अस्वीकृति का कारण।'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
