<?php

namespace App\Filament\Resources\Withdrawals\Tables;

use App\Models\Withdrawal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WithdrawalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('campaign.title')
                    ->label('Kampanye')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                
                TextColumn::make('campaign.user.name')
                    ->label('Creator')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),
                
                TextColumn::make('fee_amount')
                    ->label('Fee')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(),
                
                TextColumn::make('net_amount')
                    ->label('Diterima')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold'),
                
                TextColumn::make('method')
                    ->label('Metode')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bank_transfer' => '🏦 Bank',
                        'e_wallet' => '📱 E-Wallet',
                        default => $state,
                    })
                    ->sortable(),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => Withdrawal::STATUS_PENDING,
                        'info' => Withdrawal::STATUS_APPROVED,
                        'primary' => Withdrawal::STATUS_PROCESSING,
                        'success' => Withdrawal::STATUS_COMPLETED,
                        'danger' => Withdrawal::STATUS_REJECTED,
                        'secondary' => Withdrawal::STATUS_CANCELLED,
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Withdrawal::STATUS_PENDING => '⏳ Pending',
                        Withdrawal::STATUS_APPROVED => '✅ Approved',
                        Withdrawal::STATUS_PROCESSING => '🔄 Processing',
                        Withdrawal::STATUS_COMPLETED => '✅ Completed',
                        Withdrawal::STATUS_REJECTED => '❌ Rejected',
                        Withdrawal::STATUS_CANCELLED => '🚫 Cancelled',
                        default => $state,
                    })
                    ->sortable(),
                
                TextColumn::make('reference_number')
                    ->label('Ref. Number')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(),
                
                TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->placeholder('—')
                    ->toggleable(),
                
                TextColumn::make('requested_at')
                    ->label('Requested')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($state) => $state?->format('d F Y, H:i:s')),
                
                TextColumn::make('approved_at')
                    ->label('Approved')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Withdrawal::STATUS_PENDING => '⏳ Pending',
                        Withdrawal::STATUS_APPROVED => '✅ Approved',
                        Withdrawal::STATUS_PROCESSING => '🔄 Processing',
                        Withdrawal::STATUS_COMPLETED => '✅ Completed',
                        Withdrawal::STATUS_REJECTED => '❌ Rejected',
                        Withdrawal::STATUS_CANCELLED => '🚫 Cancelled',
                    ])
                    ->multiple(),
                
                SelectFilter::make('method')
                    ->label('Metode')
                    ->options([
                        'bank_transfer' => '🏦 Transfer Bank',
                        'e_wallet' => '📱 E-Wallet',
                    ]),
                
                Filter::make('amount_range')
                    ->label('Range Amount')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('amount_from')
                            ->label('Dari')
                            ->numeric()
                            ->prefix('Rp'),
                        \Filament\Forms\Components\TextInput::make('amount_to')
                            ->label('Sampai')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-m-eye'),
                    
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Withdrawal')
                    ->modalDescription('Are you sure you want to approve this withdrawal request?')
                    ->action(function (Withdrawal $record) {
                        $record->updateStatus(Withdrawal::STATUS_APPROVED);
                    })
                    ->visible(fn (Withdrawal $record) => $record->status === Withdrawal::STATUS_PENDING),
                    
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Withdrawal')
                    ->modalDescription('Are you sure you want to reject this withdrawal request?')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Rejection Reason')
                            ->required()
                            ->placeholder('Please provide a reason for rejection...')
                    ])
                    ->action(function (Withdrawal $record, array $data) {
                        $record->updateStatus(Withdrawal::STATUS_REJECTED, $data['notes']);
                    })
                    ->visible(fn (Withdrawal $record) => $record->status === Withdrawal::STATUS_PENDING),
                    
                Action::make('complete')
                    ->label('Mark Complete')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Complete Withdrawal')
                    ->modalDescription('Mark this withdrawal as completed. This action cannot be undone.')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('reference_number')
                            ->label('Reference Number')
                            ->placeholder('Leave empty to auto-generate')
                            ->helperText('Format: WD + Date + Random (e.g., WD241024ABC123)')
                            ->default(fn () => Withdrawal::generateReferenceNumber())
                    ])
                    ->action(function (Withdrawal $record, array $data) {
                        $referenceNumber = !empty($data['reference_number']) 
                            ? $data['reference_number'] 
                            : Withdrawal::generateReferenceNumber();
                            
                        $record->markAsCompleted($referenceNumber);
                    })
                    ->visible(fn (Withdrawal $record) => in_array($record->status, [Withdrawal::STATUS_APPROVED, Withdrawal::STATUS_PROCESSING])),
                    
                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('requested_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
