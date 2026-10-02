<?php

namespace App\Filament\Resources\Books\Tables;

use App\Enums\BookStatus;
use App\Models\Book;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
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
                ImageColumn::make('cover')
                    ->label('Cover')
                    ->state(fn (Book $record) => $record->cover_url)
                    ->square()
                    ->size(40),

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

                TextColumn::make('digital_source')
                    ->label('Digital Asset')
                    ->badge()
                    ->state(function (Book $record): string {
                        $hasPdf = $record->media()->where('collection_name', 'pdf')->exists();
                        $hasArchive = ! empty($record->source_url);

                        if ($hasPdf && $hasArchive) {
                            return 'PDF + Archive';
                        }
                        if ($hasPdf) {
                            return 'Local PDF';
                        }
                        if ($hasArchive) {
                            return 'Archive.org';
                        }

                        return 'Metadata Only';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'PDF + Archive' => 'primary',
                        'Local PDF' => 'success',
                        'Archive.org' => 'info',
                        default => 'gray',
                    }),

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
                Action::make('reader')
                    ->label('Reader')
                    ->icon('heroicon-m-book-open')
                    ->color('info')
                    ->url(fn (Book $record) => route('books.interactive_reader', $record->slug))
                    ->openUrlInNewTab(),
                Action::make('view_page')
                    ->label('View')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Book $record) => route('books.show', $record->slug))
                    ->openUrlInNewTab(),
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
