<?php

namespace App\Filament\Resources\Media\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('file_name')
                    ->searchable(),

                TextColumn::make('collection_name')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('disk')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public' => 'success',
                        'private_pdfs' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('size')
                    ->formatStateUsing(fn ($state) => round($state / 1024, 1).' KB')
                    ->sortable(),

                TextColumn::make('mime_type')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('disk')
                    ->options([
                        'public' => 'Public Assets (WebP/Images)',
                        'private_pdfs' => 'Protected Documents (PDFs)',
                    ]),
                SelectFilter::make('collection_name')
                    ->options([
                        'covers' => 'Covers',
                        'authors' => 'Author Portraits',
                        'documents' => 'Digital Documents',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
