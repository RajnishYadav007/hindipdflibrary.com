<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Services\Contributor\ContributorRewardService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PointsLedgerRelationManager extends RelationManager
{
    protected static string $relationship = 'pointsLedger';

    protected static ?string $title = 'Library Points Ledger';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Activity / Description')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('event_type')
                    ->label('Event Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'content_published' => 'success',
                        'content_revoked' => 'danger',
                        'referral_bonus' => 'info',
                        'admin_adjustment' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('points')
                    ->label('Points')
                    ->badge()
                    ->color(fn (int $state): string => $state >= 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn (int $state): string => ($state > 0 ? '+' : '').$state)
                    ->sortable(),

                TextColumn::make('reference_id')
                    ->label('Reference')
                    ->fontFamily('mono')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('Logged By')
                    ->default('System')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Action::make('adjustPoints')
                    ->label('Adjust Points (Admin Action)')
                    ->icon('heroicon-m-plus-circle')
                    ->color('warning')
                    ->modalHeading('Manual Contributor Points Adjustment')
                    ->modalDescription('Every manual adjustment generates an auditable append-only ledger entry with mandatory reasoning.')
                    ->schema([
                        TextInput::make('points')
                            ->label('Points (+ / -)')
                            ->numeric()
                            ->required()
                            ->helperText('Enter a positive number to award (e.g. 50) or negative to deduct (e.g. -50). Cannot be 0.'),

                        TextInput::make('reason')
                            ->label('Mandatory Justification / Reason')
                            ->required()
                            ->minLength(5)
                            ->maxLength(255)
                            ->helperText('Explain why this adjustment is being made (e.g. "Editorial contest bonus", "Correction reward").'),
                    ])
                    ->action(function (array $data, ContributorRewardService $rewardService): void {
                        $user = $this->getOwnerRecord();
                        $admin = auth()->user();

                        $rewardService->adjustPointsByAdmin(
                            $user,
                            (int) $data['points'],
                            $data['reason'],
                            $admin
                        );

                        Notification::make()
                            ->title('Points Adjusted')
                            ->body("Successfully recorded adjustment of {$data['points']} points for {$user->name}.")
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
