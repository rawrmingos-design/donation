<?php

namespace App\Filament\Resources\FundraiserApplicationResource\Pages;

use App\Filament\Resources\FundraiserApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\Tabs;

use Illuminate\Database\Eloquent\Builder;

class ListFundraiserApplications extends ListRecords
{
    protected static string $resource = FundraiserApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Applications'),
            
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => $this->getModel()::where('status', 'pending')->count())
                ->badgeColor('warning'),
                
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved'))
                ->badge(fn () => $this->getModel()::where('status', 'approved')->count())
                ->badgeColor('success'),
                
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected'))
                ->badge(fn () => $this->getModel()::where('status', 'rejected')->count())
                ->badgeColor('danger'),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'pending';
    }
}
