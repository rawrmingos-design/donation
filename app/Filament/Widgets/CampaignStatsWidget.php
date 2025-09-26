<?php

namespace App\Filament\Widgets;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class CampaignStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calculate stats
        $totalCampaigns = Campaign::count();
        $activeCampaigns = Campaign::where('status', 'active')->count();
        
        $totalDonations = Donation::sum('amount');
        $donationCount = Donation::count();
        
        $successfulTransactions = Transaction::where('status', 'success')->count();
        $totalTransactions = Transaction::count();
        $successRate = $totalTransactions > 0 ? ($successfulTransactions / $totalTransactions) * 100 : 0;
        
        // Monthly growth calculation
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        
        $currentMonthAmount = Donation::where('created_at', '>=', $currentMonth)->sum('amount');
        $lastMonthAmount = Donation::whereBetween('created_at', [$lastMonth, $currentMonth])->sum('amount');
        
        $monthlyGrowth = $lastMonthAmount > 0 ? 
            (($currentMonthAmount - $lastMonthAmount) / $lastMonthAmount) * 100 : 0;

        return [
            Stat::make('Total Campaigns', $totalCampaigns)
                ->description($activeCampaigns . ' currently active')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
                
            Stat::make('Total Donations', 'Rp ' . number_format($totalDonations))
                ->description($donationCount . ' transactions')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
                
            Stat::make('Success Rate', number_format($successRate, 1) . '%')
                ->description('Payment success rate')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($successRate >= 90 ? 'success' : ($successRate >= 75 ? 'warning' : 'danger')),
                
            Stat::make('Monthly Growth', ($monthlyGrowth >= 0 ? '+' : '') . number_format($monthlyGrowth, 1) . '%')
                ->description('Compared to last month')
                ->descriptionIcon($monthlyGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyGrowth >= 0 ? 'success' : 'danger'),
        ];
    }
}
