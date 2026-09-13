<?php

namespace App\Filament\Resources\Reviews\Tables;

use App\Enums\ReviewStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('book.slug')
                    ->label('Book')
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => str_repeat('★', (int) $state).str_repeat('☆', 5 - (int) $state))
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof ReviewStatus ? $state->value : (string) $state) {
                        ReviewStatus::Approved->value => 'success',
                        ReviewStatus::Pending->value => 'warning',
                        ReviewStatus::Rejected->value => 'danger',
                        ReviewStatus::Spam->value => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('title')
                    ->limit(25)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        ReviewStatus::Pending->value => 'Pending Review',
                        ReviewStatus::Approved->value => 'Approved',
                        ReviewStatus::Rejected->value => 'Rejected',
                        ReviewStatus::Spam->value => 'Spam',
                    ]),
                SelectFilter::make('rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== ReviewStatus::Approved && $record->status !== ReviewStatus::Approved->value)
                    ->action(fn ($record) => $record->update(['status' => ReviewStatus::Approved->value])),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== ReviewStatus::Rejected && $record->status !== ReviewStatus::Rejected->value)
                    ->action(fn ($record) => $record->update(['status' => ReviewStatus::Rejected->value])),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['status' => ReviewStatus::Approved->value]))
                        ->requiresConfirmation(),

                    BulkAction::make('reject_selected')
                        ->label('Reject Selected')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['status' => ReviewStatus::Rejected->value]))
                        ->requiresConfirmation(),

                    BulkAction::make('spam_selected')
                        ->label('Mark as Spam')
                        ->icon('heroicon-m-shield-exclamation')
                        ->color('gray')
                        ->action(fn (Collection $records) => $records->each->update(['status' => ReviewStatus::Spam->value]))
                        ->requiresConfirmation(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
