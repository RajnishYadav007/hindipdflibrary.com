<?php

namespace App\Filament\Resources\Audiobooks\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class AudiobookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Audiobook Details')
                    ->tabs([
                        Tab::make('General Information')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
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

                                Select::make('language_id')
                                    ->label('Language')
                                    ->relationship('language', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                TextInput::make('narrator')
                                    ->label('Narrator (वाचक)')
                                    ->maxLength(150),

                                TextInput::make('duration_seconds')
                                    ->label('Duration in Seconds')
                                    ->numeric()
                                    ->nullable()
                                    ->helperText('कुल सेकंड में अवधि (जैसे 3600 = 1 घंटा). अज्ञात होने पर खाली छोड़ें।'),

                                Select::make('book_id')
                                    ->label('Related Book (Optional)')
                                    ->relationship('book', 'slug')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
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
                                            ->label('Title')
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(4),

                                        TextInput::make('seo_title')
                                            ->maxLength(255),

                                        Textarea::make('seo_description')
                                            ->rows(2),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1),
                            ]),

                        Tab::make('Media & Audio')
                            ->icon('heroicon-m-speaker-wave')
                            ->schema([
                                FileUpload::make('cover_image')
                                    ->label('Cover Art (कवर चित्र)')
                                    ->image()
                                    ->disk('public')
                                    ->directory('covers/audiobooks'),

                                FileUpload::make('audio_file')
                                    ->label('Full Audio File (MP3 / M4A / AAC)')
                                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/mp4', 'audio/x-m4a', 'audio/aac'])
                                    ->disk('public')
                                    ->directory('audio/audiobooks')
                                    ->maxSize(51200),

                                TextInput::make('external_audio_url')
                                    ->label('External Audio URL (Archive.org etc.)')
                                    ->url()
                                    ->maxLength(500),
                            ]),

                        Tab::make('Chapters (अध्याय)')
                            ->icon('heroicon-m-list-bullet')
                            ->schema([
                                Repeater::make('chapters')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('chapter_number')
                                            ->numeric()
                                            ->required(),

                                        Select::make('status')
                                            ->options([
                                                'draft' => 'Draft (अप्रकाशित)',
                                                'published' => 'Published (प्रकाशित)',
                                            ])
                                            ->default('published')
                                            ->required(),

                                        TextInput::make('duration_seconds')
                                            ->numeric()
                                            ->nullable(),

                                        TextInput::make('external_audio_url')
                                            ->url()
                                            ->maxLength(500),
                                    ])
                                    ->columns(4)
                                    ->collapsible(),
                            ]),

                        Tab::make('Legal & Moderation')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Select::make('submission_type')
                                    ->options([
                                        'editorial' => 'Editorial (संपादकीय टीम)',
                                        'user_submission' => 'User Submission (उपयोगकर्ता प्रस्तुति)',
                                    ])
                                    ->default('editorial'),

                                TextInput::make('copyright_status')
                                    ->maxLength(50),

                                TextInput::make('license_type')
                                    ->maxLength(100),

                                TextInput::make('rights_holder')
                                    ->maxLength(255),

                                Textarea::make('permission_notes')
                                    ->rows(2),

                                Textarea::make('admin_notes')
                                    ->label('Admin Moderation Notes (आंतरिक / अस्वीकृति टिप्पणी)')
                                    ->rows(3)
                                    ->helperText('यदि अस्वीकृत किया गया है तो कारण यहाँ दर्ज करें। यह उपयोगकर्ता को दिखेगा।'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
