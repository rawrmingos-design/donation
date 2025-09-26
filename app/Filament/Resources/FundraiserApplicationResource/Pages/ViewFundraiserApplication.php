<?php

namespace App\Filament\Resources\FundraiserApplicationResource\Pages;

use App\Filament\Resources\FundraiserApplicationResource;
use App\Models\FundraiserApplication;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewFundraiserApplication extends ViewRecord
{
    protected static string $resource = FundraiserApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            
            Action::make('approve')
                ->label('Approve Application')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Approve Fundraiser Application')
                ->modalDescription('Are you sure you want to approve this fundraiser application? This will change the user role to creator.')
                ->form([
                    Forms\Components\Textarea::make('admin_notes')
                        ->label('Approval Notes')
                        ->placeholder('Optional notes for approval...')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'status' => 'approved',
                        'admin_notes' => $data['admin_notes'] ?? null,
                        'reviewed_at' => now(),
                        'reviewed_by' => Auth::id(),
                    ]);

                    // Update user role to creator
                    $this->record->user->update(['role' => 'creator']);

                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (): bool => $this->record->status === 'pending'),

            Action::make('reject')
                ->label('Reject Application')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reject Fundraiser Application')
                ->modalDescription('Are you sure you want to reject this fundraiser application?')
                ->form([
                    Forms\Components\Textarea::make('admin_notes')
                        ->label('Rejection Reason')
                        ->placeholder('Please provide a reason for rejection...')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'status' => 'rejected',
                        'admin_notes' => $data['admin_notes'],
                        'reviewed_at' => now(),
                        'reviewed_by' => Auth::id(),
                    ]);

                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (): bool => $this->record->status === 'pending'),

            Action::make('reset')
                ->label('Reset to Pending')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reset Application Status')
                ->modalDescription('This will reset the application status to pending and revert user role changes.')
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'pending',
                        'admin_notes' => null,
                        'reviewed_at' => null,
                        'reviewed_by' => null,
                    ]);

                    // If user was creator, revert to donor
                    if ($this->record->user->role === 'creator') {
                        $this->record->user->update(['role' => 'donor']);
                    }

                    $this->redirect($this->getResource()::getUrl('index'));
                })
                ->visible(fn (): bool => $this->record->status !== 'pending'),
        ];
    }
}
