<?php

namespace App\Filament\Resources\Seo\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SeoMetasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('seoable_type')
                    ->label('Entity Type')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('seoable_id')
                    ->label('Entity ID')
                    ->sortable(),

                TextColumn::make('locale')
                    ->badge()
                    ->sortable(),

                TextColumn::make('robots')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('canonical_url')
                    ->limit(35)
                    ->searchable(),

                TextColumn::make('schema_type')
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('locale')
                    ->options([
                        'hi' => 'Hindi',
                        'en' => 'English',
                        'sa' => 'Sanskrit',
                    ]),
                SelectFilter::make('robots')
                    ->options([
                        'index, follow' => 'index, follow',
                        'noindex, follow' => 'noindex, follow',
                        'noindex, nofollow' => 'noindex, nofollow',
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
