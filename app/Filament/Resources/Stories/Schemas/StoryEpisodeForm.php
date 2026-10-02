<?php

namespace App\Filament\Resources\Stories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class StoryEpisodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Episode Details')
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Select::make('story_series_id')
                                    ->label('Story Series (श्रृंखला)')
                                    ->relationship('series', 'slug')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('episode_number')
                                    ->label('Episode Number (अध्याय क्रमांक)')
                                    ->numeric()
                                    ->required(),

                                Select::make('status')
                                    ->label('Status (स्थिति)')
                                    ->options([
                                        'draft' => 'Draft (अप्रकाशित - Default)',
                                        'pending_review' => 'Pending Review (समीक्षाधीन)',
                                        'published' => 'Published (प्रकाशित)',
                                        'archived' => 'Archived (पुरालेखित)',
                                    ])
                                    ->default('draft') // Never default published!
                                    ->required(),

                                Select::make('narration_type')
                                    ->options([
                                        'tts' => 'Browser Text-to-Speech (TTS)',
                                        'uploaded' => 'Uploaded Audio Narration',
                                        'none' => 'Text Only',
                                    ])
                                    ->default('tts')
                                    ->required(),

                                TextInput::make('duration_seconds')
                                    ->label('Duration in Seconds (Optional)')
                                    ->numeric()
                                    ->nullable(),
                            ]),

                        Tab::make('Story Content & Translations')
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

                                        TextInput::make('slug')
                                            ->required()
                                            ->maxLength(150),

                                        RichEditor::make('content')
                                            ->label('Story Content (कहानी की पूरी सामग्री)')
                                            ->required()
                                            ->columnSpanFull(),

                                        Textarea::make('description')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(1),
                            ]),

                        Tab::make('Audio Narration File')
                            ->icon('heroicon-m-speaker-wave')
                            ->schema([
                                FileUpload::make('audio_file')
                                    ->label('Narration Audio (MP3 / M4A)')
                                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/mp4', 'audio/x-m4a'])
                                    ->disk('public')
                                    ->directory('audio/stories')
                                    ->maxSize(51200),

                                TextInput::make('external_audio_url')
                                    ->label('External Audio URL')
                                    ->url()
                                    ->maxLength(500),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
