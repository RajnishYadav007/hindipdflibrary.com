<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use Filament\Forms\Components\FileUpload;
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
                                        BookStatus::Draft->value => 'Draft (अप्रकाशित)',
                                        BookStatus::Published->value => 'Published (प्रकाशित)',
                                        BookStatus::Archived->value => 'Archived (पुरालेखित)',
                                    ])
                                    ->default(BookStatus::Draft->value)
                                    ->required(),

                                Select::make('original_language_id')
                                    ->label('Original Language (मूल भाषा)')
                                    ->relationship('originalLanguage', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Select original language'),

                                Select::make('content_type')
                                    ->label('Content / Spiritual Type (प्रारूप)')
                                    ->options([
                                        'book' => '📖 पुस्तक / ग्रंथ (Book / Scripture)',
                                        'chalisa' => '🚩 चालीसा (Chalisa)',
                                        'aarti' => '🪔 आरती (Aarti)',
                                        'stotra' => '⚡ स्तोत्र / पाठ (Stotra)',
                                        'mantra' => '🕉️ मंत्र / सूक्त (Mantra / Suktam)',
                                    ])
                                    ->default('book')
                                    ->required(),

                                Select::make('deity')
                                    ->label('Deity / Topic Entity (देवता / मुख्य विषय)')
                                    ->options([
                                        'hanuman' => 'श्री हनुमान जी (Lord Hanuman)',
                                        'shiva' => 'भगवान शिव (Lord Shiva)',
                                        'ram' => 'प्रभु श्री राम (Lord Rama)',
                                        'krishna' => 'भगवान श्री कृष्ण (Lord Krishna)',
                                        'ganesh' => 'श्री गणेश (Lord Ganesha)',
                                        'durga' => 'मां दुर्गा (Maa Durga)',
                                        'lakshmi' => 'माता लक्ष्मी (Maa Lakshmi)',
                                        'saraswati' => 'माता सरस्वती (Maa Saraswati)',
                                        'vishnu' => 'भगवान विष्णु (Lord Vishnu)',
                                        'surya' => 'भगवान सूर्य (Lord Surya)',
                                        'vedas' => 'वेद एवं उपनिषद् (Vedas & Upanishads)',
                                    ])
                                    ->searchable()
                                    ->placeholder('Select Divine Entity')
                                    ->helperText('Associates this content with its Divine Topic Cluster for automated internal cross-linking.'),

                                FileUpload::make('cover_image')
                                    ->label('Book Cover Image (पुस्तक का मुखपृष्ठ)')
                                    ->image()
                                    ->disk('public')
                                    ->directory('covers')
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->maxSize(10240)
                                    ->columnSpanFull()
                                    ->helperText('Upload front book cover (JPG, PNG, WebP). If omitted, the front cover will be automatically extracted from the first page of the PDF or fetched from Archive.org.'),
                            ])
                            ->columns(3),

                        Tab::make('Classification')
                            ->icon('heroicon-m-tag')
                            ->schema([
                                Select::make('authors')
                                    ->label('Authors / Contributors')
                                    ->relationship('authors', 'slug')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => ($record->translations->first()?->name ?? $record->slug).' ('.$record->slug.')')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->helperText('Select one or more authors or translators.'),

                                Select::make('categories')
                                    ->relationship('categories', 'slug')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => ($record->translations->first()?->name ?? $record->slug).' ('.$record->slug.')')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),

                                Select::make('genres')
                                    ->relationship('genres', 'slug')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => ($record->translations->first()?->name ?? $record->slug).' ('.$record->slug.')')
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

                                        Textarea::make('lyrics')
                                            ->label('Lyrics & Recitation Text / पाठ, चालीसा, आरती व स्तोत्र')
                                            ->placeholder('सम्पूर्ण चालीसा, स्तोत्र, आरती, भजन अथवा श्लोक यहाँ लिखें (Enter devotional lyrics or recitation text here)...')
                                            ->helperText('Enter full lyrical verses, chaupais, dohas, or devotional text. Displays on the book page with a clean devotional layout, font-size adjustment (A-/A+), copy button, and audio reading support.')
                                            ->rows(10)
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
                                    ->label('Archive / Source URL (आर्काइव / बाहरी स्रोत लिंक)')
                                    ->url()
                                    ->maxLength(500)
                                    ->placeholder('https://archive.org/details/your-book-id')
                                    ->helperText('Enter Internet Archive (archive.org/details/...) or external repository URL. Automatically powers the interactive audio & page flip reader.')
                                    ->columnSpanFull(),

                                FileUpload::make('pdf_file')
                                    ->label('Upload Book PDF (लोकल PDF फ़ाइल अपलोड)')
                                    ->disk('private_pdfs')
                                    ->directory('')
                                    ->visibility('private')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->maxSize(102400)
                                    ->helperText('Upload private PDF document for platform-hosted reading and downloading. Stored securely outside public webroot.')
                                    ->columnSpanFull(),

                                Select::make('copyright_status')
                                    ->label('Copyright & Hosting Rights (कॉपीराइट स्थिति)')
                                    ->options(collect(CopyrightStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                                    ->default(CopyrightStatus::PublicDomain->value)
                                    ->required()
                                    ->helperText('Determines legal rights for local hosting and direct downloading.'),

                                TextInput::make('license_type')
                                    ->placeholder('e.g. Public Domain, CC-BY-SA 4.0')
                                    ->maxLength(100),

                                TextInput::make('rights_holder')
                                    ->placeholder('e.g. Public Domain, Government of India')
                                    ->maxLength(255),

                                Textarea::make('permission_notes')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->placeholder('Document copyright research, author demise date (>60 years in India), or publisher permission proof.'),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
