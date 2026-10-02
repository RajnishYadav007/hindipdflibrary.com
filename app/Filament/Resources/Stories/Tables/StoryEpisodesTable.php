<?php

namespace App\Filament\Resources\Stories\Tables;

use App\Models\StoryEpisode;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StoryEpisodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('series.slug')
                    ->label('Series')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('episode_number')
                    ->label('Ep. #')
                    ->sortable(),

                TextColumn::make('translations.title')
                    ->label('Title')
                    ->searchable()
                    ->limit(35),

                TextColumn::make('narration_type')
                    ->label('Audio Mode')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'uploaded' => 'success',
                        'tts' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'pending_review' => 'warning',
                        'rejected' => 'danger',
                        'draft' => 'gray',
                        default => 'secondary',
                    })
                    ->sortable(),

                TextColumn::make('view_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending_review' => 'Pending Review',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (StoryEpisode $record) => $record->status !== 'published')
                    ->requiresConfirmation()
                    ->action(fn (StoryEpisode $record) => $record->update(['status' => 'published'])),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
