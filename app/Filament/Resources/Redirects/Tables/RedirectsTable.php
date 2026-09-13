<?php

namespace App\Filament\Resources\Redirects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RedirectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('source_path')
                    ->label('Source Path')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('destination_path')
                    ->label('Destination')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('status_code')
                    ->label('Code')
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        301, 308 => 'success',
                        302, 307 => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('hit_count')
                    ->label('Hits')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('last_accessed_at')
                    ->label('Last Intercepted')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
                SelectFilter::make('status_code')
                    ->options([
                        301 => '301 Moved Permanently',
                        302 => '302 Found',
                        307 => '307 Temporary',
                        308 => '308 Permanent',
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
