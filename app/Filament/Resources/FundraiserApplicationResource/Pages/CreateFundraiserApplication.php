<?php

namespace App\Filament\Resources\FundraiserApplicationResource\Pages;

use App\Filament\Resources\FundraiserApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFundraiserApplication extends CreateRecord
{
    protected static string $resource = FundraiserApplicationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
