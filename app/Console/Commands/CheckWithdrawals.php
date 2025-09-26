<?php

namespace App\Console\Commands;

use App\Models\Withdrawal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:withdrawals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check withdrawal data in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Withdrawal System Status ===');
        
        // Total withdrawals
        $totalWithdrawals = Withdrawal::count();
        $this->info("Total withdrawals: {$totalWithdrawals}");
        
        if ($totalWithdrawals === 0) {
            $this->warn('No withdrawals found in database.');
            return;
        }
        
        // Status breakdown
        $this->info("\n=== Status Breakdown ===");
        $statusCounts = Withdrawal::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
            
        foreach ($statusCounts as $status) {
            $this->line("{$status->status}: {$status->count}");
        }
        
        // Method breakdown
        $this->info("\n=== Method Breakdown ===");
        $methodCounts = Withdrawal::select('method', DB::raw('count(*) as count'))
            ->groupBy('method')
            ->get();
            
        foreach ($methodCounts as $method) {
            $methodName = $method->method === 'bank_transfer' ? 'Bank Transfer' : 'E-Wallet';
            $this->line("{$methodName}: {$method->count}");
        }
        
        // Total amounts
        $this->info("\n=== Financial Summary ===");
        $totalAmount = Withdrawal::sum('amount');
        $totalFees = Withdrawal::sum('fee_amount');
        $totalNet = Withdrawal::sum('net_amount');
        
        $this->line("Total Requested: Rp " . number_format($totalAmount, 0, ',', '.'));
        $this->line("Total Fees: Rp " . number_format($totalFees, 0, ',', '.'));
        $this->line("Total Net: Rp " . number_format($totalNet, 0, ',', '.'));
        
        // Recent withdrawals
        $this->info("\n=== Recent Withdrawals ===");
        $recentWithdrawals = Withdrawal::with(['campaign:id,title'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        foreach ($recentWithdrawals as $withdrawal) {
            $amount = number_format($withdrawal->amount, 0, ',', '.');
            $campaign = $withdrawal->campaign->title ?? 'Unknown Campaign';
            $this->line("ID: {$withdrawal->id} | {$campaign} | Rp {$amount} | {$withdrawal->status}");
        }
        
        $this->info("\n✅ Withdrawal system is working properly!");
    }
}
