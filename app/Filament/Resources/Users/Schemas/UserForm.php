<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Account & Access')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->helperText('Leave blank to keep existing password when editing.'),

                        Select::make('roles')
                            ->label('Assigned Roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Assign administrative or moderation roles.'),

                        Toggle::make('is_admin')
                            ->label('Direct Admin Access Flag')
                            ->default(false),
                    ])
                    ->columns(2),

                Section::make('Contributor Recognition & Library Points')
                    ->schema([
                        TextInput::make('contributor_level')
                            ->label('Contributor Level')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn (?string $state): string => ucwords(str_replace('_', ' ', $state ?? 'reader'))),

                        TextInput::make('total_points')
                            ->label('Cached Library Points')
                            ->disabled()
                            ->dehydrated(false)
                            ->numeric()
                            ->helperText('Points are auditable and derived from the append-only ledger below.'),

                        TextInput::make('referral_code')
                            ->label('Referral Code')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }
}
