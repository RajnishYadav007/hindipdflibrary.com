<?php

namespace App\Filament\Resources\Stories\Tables;

use App\Models\StorySeries;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StorySeriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('translations.title')
                    ->label('Title')
                    ->searchable()
                    ->limit(35),

                TextColumn::make('age_group')
                    ->label('Age Group')
                    ->badge()
                    ->color('info')
                    ->sortable(),

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

                TextColumn::make('episodes_count')
                    ->label('Episodes')
                    ->counts('episodes')
                    ->sortable(),

                TextColumn::make('submitter.name')
                    ->label('Submitted By')
                    ->placeholder('Editorial'),
            ])
            ->filters([
                SelectFilter::make('age_group')
                    ->options([
                        '3-5' => '3-5 years',
                        '6-8' => '6-8 years',
                        '9-12' => '9-12 years',
                        'all' => 'All Ages',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending_review' => 'Pending Review',
                        'published' => 'Published',
                        'rejected' => 'Rejected',
                        'archived' => 'Archived',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (StorySeries $record) => $record->status === 'pending_review')
                    ->requiresConfirmation()
                    ->action(function (StorySeries $record) {
                        $record->update([
                            'status' => 'published',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'published_at' => $record->published_at ?? now(),
                        ]);
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn (StorySeries $record) => $record->status === 'pending_review')
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (StorySeries $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'admin_notes' => $data['admin_notes'],
                        ]);
                    }),

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
