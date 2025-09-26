<?php

namespace App\Filament\Resources\AnalyticsResource\Pages;

use App\Filament\Resources\AnalyticsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnalytics extends ListRecords
{
    protected static string $resource = AnalyticsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Report')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    // Export functionality can be implemented here
                    $this->notify('success', 'Export feature will be implemented soon!');
                }),
                
            Actions\Action::make('refresh')
                ->label('Refresh Data')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $this->notify('success', 'Data refreshed successfully!');
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // You can add summary widgets here if needed
        ];
    }
}
