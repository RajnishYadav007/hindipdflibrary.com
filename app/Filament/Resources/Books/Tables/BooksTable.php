<?php

namespace App\Filament\Resources\Books\Tables;

use App\Enums\BookStatus;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('translations.title')
                    ->label('Title')
                    ->searchable()
                    ->limit(25)
                    ->badge(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof BookStatus ? $state->value : (string) $state) {
                        BookStatus::Published->value => 'success',
                        BookStatus::Draft->value => 'warning',
                        BookStatus::Archived->value => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('authors.slug')
                    ->badge()
                    ->color('info')
                    ->label('Authors'),

                TextColumn::make('categories.slug')
                    ->badge()
                    ->color('gray')
                    ->label('Categories'),

                TextColumn::make('rating_average')
                    ->label('Rating')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('publication_year')
                    ->label('Year')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        BookStatus::Draft->value => 'Draft',
                        BookStatus::Published->value => 'Published',
                        BookStatus::Archived->value => 'Archived',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')
                        ->label('Publish Selected')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['status' => BookStatus::Published->value]))
                        ->requiresConfirmation(),

                    BulkAction::make('archive')
                        ->label('Archive Selected')
                        ->icon('heroicon-m-archive-box')
                        ->color('warning')
                        ->action(fn (Collection $records) => $records->each->update(['status' => BookStatus::Archived->value]))
                        ->requiresConfirmation(),

                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
