<?php

namespace App\Filament\Resources\FundraiserApplicationResource\Pages;

use App\Filament\Resources\FundraiserApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditFundraiserApplication extends EditRecord
{
    protected static string $resource = FundraiserApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If status is being changed to approved or rejected, update review fields
        if (isset($data['status']) && in_array($data['status'], ['approved', 'rejected'])) {
            $data['reviewed_at'] = now();
            $data['reviewed_by'] = Auth::id();

            // If approving, update user role to creator
            if ($data['status'] === 'approved') {
                $this->record->user->update(['role' => 'creator']);
            }
            // If rejecting after being approved, revert user role to donor
            elseif ($data['status'] === 'rejected' && $this->record->status === 'approved') {
                $this->record->user->update(['role' => 'donor']);
            }
        }
        // If status is being reset to pending
        elseif (isset($data['status']) && $data['status'] === 'pending') {
            $data['reviewed_at'] = null;
            $data['reviewed_by'] = null;
            $data['admin_notes'] = null;

            // If user was creator, revert to donor
            if ($this->record->user->role === 'creator') {
                $this->record->user->update(['role' => 'donor']);
            }
        }

        return $data;
    }
}
