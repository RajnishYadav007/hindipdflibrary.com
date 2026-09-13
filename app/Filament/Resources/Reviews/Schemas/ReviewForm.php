<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Enums\ReviewStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Review Details & Moderation')
                    ->schema([
                        Select::make('book_id')
                            ->relationship('book', 'slug')
                            ->disabled(),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled(),

                        TextInput::make('rating')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->disabled(),

                        Select::make('status')
                            ->options([
                                ReviewStatus::Pending->value => 'Pending Review',
                                ReviewStatus::Approved->value => 'Approved',
                                ReviewStatus::Rejected->value => 'Rejected',
                                ReviewStatus::Spam->value => 'Spam',
                            ])
                            ->required(),

                        Toggle::make('is_verified')
                            ->label('Verified Reader Badge'),

                        TextInput::make('title')
                            ->maxLength(150),

                        Textarea::make('body')
                            ->rows(5)
                            ->columnSpanFull(),

                        TextInput::make('ip_hash')
                            ->label('Anonymized IP Hash')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }
}
