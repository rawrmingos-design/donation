<?php

namespace App\Filament\Widgets;

use App\Models\Withdrawal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class WithdrawalStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get withdrawal statistics
        $totalWithdrawals = Withdrawal::count();
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        $completedWithdrawals = Withdrawal::where('status', 'completed')->count();
        $totalAmount = Withdrawal::where('status', 'completed')->sum('amount');
        $totalFees = Withdrawal::where('status', 'completed')->sum('fee_amount');
        
        // Get monthly growth
        $currentMonth = Withdrawal::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $lastMonth = Withdrawal::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $monthlyGrowth = $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;
        
        return [
            Stat::make('Total Withdrawals', $totalWithdrawals)
                ->description('All withdrawal requests')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
                
            Stat::make('Pending Approvals', $pendingWithdrawals)
                ->description('Waiting for approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingWithdrawals > 0 ? 'warning' : 'success'),
                
            Stat::make('Completed', $completedWithdrawals)
                ->description('Successfully processed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Total Disbursed', 'Rp ' . number_format($totalAmount, 0, ',', '.'))
                ->description('Total amount disbursed')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
                
            Stat::make('Platform Fees', 'Rp ' . number_format($totalFees, 0, ',', '.'))
                ->description('Total fees collected')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
                
            Stat::make('Monthly Growth', number_format($monthlyGrowth, 1) . '%')
                ->description($monthlyGrowth >= 0 ? 'Increase from last month' : 'Decrease from last month')
                ->descriptionIcon($monthlyGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyGrowth >= 0 ? 'success' : 'danger'),
        ];
    }
    
    protected function getColumns(): int
    {
        return 3;
    }
}
