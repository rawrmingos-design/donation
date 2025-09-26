<?php

namespace App\Filament\Pages;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Transaction;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use UnitEnum;
use BackedEnum;
class CampaignAnalytics extends Page
{
    protected static BackedEnum | null | string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationLabel = 'Campaign Analytics';
    
    protected static ?string $title = 'Campaign Analytics Dashboard';
    
    public string $view = 'filament.pages.campaign-analytics';
    
    protected static UnitEnum | string | null $navigationGroup = 'Analytics';
    
    protected static ?int $navigationSort = 1;

    public function getStats(): array
    {
        // Total Campaigns
        $totalCampaigns = Campaign::count();
        $activeCampaigns = Campaign::where('status', 'active')->count();
        $completedCampaigns = Campaign::where('status', 'completed')->count();
        
        // Total Donations
        $totalDonations = Donation::count();
        $totalDonationAmount = Donation::sum('amount');
        $avgDonationAmount = $totalDonations > 0 ? $totalDonationAmount / $totalDonations : 0;
        
        // Success Rate
        $successfulTransactions = Transaction::where('status', 'success')->count();
        $totalTransactions = Transaction::count();
        $successRate = $totalTransactions > 0 ? ($successfulTransactions / $totalTransactions) * 100 : 0;
        
        // Monthly Growth
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        
        $currentMonthDonations = Donation::where('created_at', '>=', $currentMonth)->sum('amount');
        $lastMonthDonations = Donation::whereBetween('created_at', [$lastMonth, $currentMonth])->sum('amount');
        
        $monthlyGrowth = $lastMonthDonations > 0 ? 
            (($currentMonthDonations - $lastMonthDonations) / $lastMonthDonations) * 100 : 0;

        return [
            'totalCampaigns' => $totalCampaigns,
            'activeCampaigns' => $activeCampaigns,
            'completedCampaigns' => $completedCampaigns,
            'totalDonations' => $totalDonations,
            'totalDonationAmount' => $totalDonationAmount,
            'avgDonationAmount' => $avgDonationAmount,
            'successRate' => $successRate,
            'monthlyGrowth' => $monthlyGrowth,
        ];
    }

    public function getTopCampaigns(): array
    {
        return Campaign::select('campaigns.*')
            ->selectRaw('COALESCE(SUM(donations.amount), 0) as total_raised')
            ->selectRaw('COUNT(donations.id) as donation_count')
            ->leftJoin('donations', 'campaigns.id', '=', 'donations.campaign_id')
            ->groupBy('campaigns.id')
            ->orderByDesc('total_raised')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function getRecentDonations(): array
    {
        return Donation::with(['campaign', 'donor'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function getCampaignsByCategory(): array
    {
        return Campaign::select('categories.name as category_name')
            ->selectRaw('COUNT(campaigns.id) as campaign_count')
            ->selectRaw('COALESCE(SUM(donations.amount), 0) as total_raised')
            ->leftJoin('categories', 'campaigns.category_id', '=', 'categories.id')
            ->leftJoin('donations', 'campaigns.id', '=', 'donations.campaign_id')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_raised')
            ->get()
            ->toArray();
    }

    public function getDonationTrends(): array
    {
        $last30Days = collect(range(29, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            $amount = Donation::whereDate('created_at', $date->toDateString())->sum('amount');
            $count = Donation::whereDate('created_at', $date->toDateString())->count();
            
            return [
                'date' => $date->format('Y-m-d'),
                'amount' => $amount,
                'count' => $count,
            ];
        });

        return $last30Days->toArray();
    }

    public function getPaymentMethodStats(): array
    {
        return Transaction::select('payment_providers.name as provider_name')
            ->selectRaw('COUNT(transactions.id) as transaction_count')
            ->selectRaw('SUM(transactions.amount) as total_amount')
            ->join('payment_providers', 'transactions.provider_id', '=', 'payment_providers.id')
            ->where('transactions.status', 'success')
            ->groupBy('payment_providers.id', 'payment_providers.name')
            ->orderByDesc('total_amount')
            ->get()
            ->toArray();
    }
}
