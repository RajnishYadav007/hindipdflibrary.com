<?php

namespace App\Filament\Resources\Redirects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RedirectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('URL Redirect Rule')
                    ->description('Configure incoming URL path forwarding with HTTP status codes.')
                    ->schema([
                        TextInput::make('source_path')
                            ->label('Source Path')
                            ->helperText('Relative URL path to intercept (e.g. /old-library-book-123).')
                            ->required()
                            ->maxLength(500)
                            ->placeholder('/old-path')
                            ->columnSpanFull(),

                        TextInput::make('destination_path')
                            ->label('Destination Path or Target URL')
                            ->helperText('Target internal path (/books/slug) or full external URL.')
                            ->required()
                            ->maxLength(500)
                            ->placeholder('/books/new-slug')
                            ->columnSpanFull(),

                        Select::make('status_code')
                            ->label('HTTP Status Code')
                            ->options([
                                301 => '301 - Moved Permanently (SEO Recommended for permanent moves)',
                                302 => '302 - Found (Temporary Redirect)',
                                307 => '307 - Temporary Redirect (Preserves HTTP Method)',
                                308 => '308 - Permanent Redirect (Preserves HTTP Method)',
                            ])
                            ->default(301)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Redirect Active')
                            ->default(true)
                            ->helperText('Disable to bypass this redirect without deleting the rule.'),

                        TextInput::make('hit_count')
                            ->label('Hit Count')
                            ->numeric()
                            ->disabled()
                            ->visibleOn('edit'),

                        TextInput::make('last_accessed_at')
                            ->label('Last Intercepted At')
                            ->disabled()
                            ->visibleOn('edit'),
                    ])
                    ->columns(2),
            ]);
    }
}
