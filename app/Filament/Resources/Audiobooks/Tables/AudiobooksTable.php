<?php

namespace App\Filament\Resources\Audiobooks\Tables;

use App\Models\Audiobook;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AudiobooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover')
                    ->label('Cover')
                    ->state(fn (Audiobook $record) => $record->cover_url)
                    ->square()
                    ->size(40),

                TextColumn::make('translations.title')
                    ->label('Title')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('narrator')
                    ->label('Narrator')
                    ->searchable()
                    ->placeholder('—'),

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

                TextColumn::make('chapters_count')
                    ->label('Chapters')
                    ->counts('chapters')
                    ->sortable(),

                TextColumn::make('submitter.name')
                    ->label('Submitted By')
                    ->placeholder('Editorial'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
                    ->visible(fn (Audiobook $record) => $record->status === 'pending_review')
                    ->requiresConfirmation()
                    ->action(function (Audiobook $record) {
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
                    ->visible(fn (Audiobook $record) => $record->status === 'pending_review')
                    ->form([
                        Textarea::make('admin_notes')
                            ->label('Rejection Reason (अस्वीकृति का कारण)')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Audiobook $record, array $data) {
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
