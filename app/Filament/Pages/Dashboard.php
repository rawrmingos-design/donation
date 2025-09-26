<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CampaignStatsWidget;
use App\Filament\Widgets\CategoryPerformanceChart;
use App\Filament\Widgets\DonationTrendsChart;
use App\Filament\Widgets\PaymentMethodChart;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\TopCampaignsTable;
use Filament\Pages\Dashboard as BaseDashboard;
use BackedEnum;
use UnitEnum;
class Dashboard extends BaseDashboard
{
    protected static BackedEnum | null | string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $title = 'Analytics Dashboard';

    public function getWidgets(): array
    {
        return [
            CampaignStatsWidget::class,
            DonationTrendsChart::class,
            CategoryPerformanceChart::class,
            PaymentMethodChart::class,
            TopCampaignsTable::class,
            RecentActivityWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 3,
        ];
    }
}
