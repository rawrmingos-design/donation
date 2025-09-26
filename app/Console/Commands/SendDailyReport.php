<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Campaign;
use App\Models\Donation;
use App\Mail\AdminDailyReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily {--date= : Date for the report (Y-m-d format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily report to admin users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::yesterday();
        $dateString = $date->format('d F Y');

        $this->info('Generating daily report for: ' . $dateString);

        try {
            // Generate report data
            $reportData = $this->generateReportData($date);

            // Get admin users
            $adminUsers = User::where('role', 'admin')->get();

            if ($adminUsers->isEmpty()) {
                $this->warn('No admin users found to send report to.');
                return 1;
            }

            // Send report to each admin
            foreach ($adminUsers as $admin) {
                Mail::to($admin->email)->send(new AdminDailyReport($reportData, $dateString));
                $this->line('Report sent to: ' . $admin->email);
            }

            $this->info('✅ Daily report sent successfully to ' . $adminUsers->count() . ' admin(s)');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Failed to send daily report: ' . $e->getMessage());
            return 1;
        }
    }

    private function generateReportData(Carbon $date): array
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();
        $previousDay = $date->copy()->subDay();

        // Donation statistics
        $donationsToday = Donation::whereBetween('created_at', [$startOfDay, $endOfDay])->count();
        $successfulDonations = Donation::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'success')->count();
        $totalAmountToday = Donation::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'success')->sum('amount');
        $averageDonation = $successfulDonations > 0 ? $totalAmountToday / $successfulDonations : 0;
        $successRate = $donationsToday > 0 ? ($successfulDonations / $donationsToday) * 100 : 0;

        // Campaign statistics
        $newCampaigns = Campaign::whereBetween('created_at', [$startOfDay, $endOfDay])->count();
        $completedCampaigns = Campaign::whereBetween('updated_at', [$startOfDay, $endOfDay])
            ->where('status', 'completed')->count();
        $activeCampaigns = Campaign::where('status', 'active')->count();
        $trendingCampaigns = Campaign::where('status', 'active')
            ->where('collected_amount', '>', 0)
            ->whereRaw('(collected_amount / target_amount) >= 0.5')
            ->count();

        // User statistics
        $newUsers = User::whereBetween('created_at', [$startOfDay, $endOfDay])->count();
        $activeDonors = Donation::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'success')
            ->distinct('donor_id')
            ->count();
        $activeCreators = Campaign::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->distinct('user_id')
            ->count();
        $totalUsers = User::count();

        // Top campaigns
        $topCampaigns = Campaign::with(['user'])
            ->whereHas('donations', function($query) use ($startOfDay, $endOfDay) {
                $query->whereBetween('created_at', [$startOfDay, $endOfDay])
                      ->where('status', 'success');
            })
            ->withCount(['donations as daily_donations' => function($query) use ($startOfDay, $endOfDay) {
                $query->whereBetween('created_at', [$startOfDay, $endOfDay])
                      ->where('status', 'success');
            }])
            ->orderBy('daily_donations', 'desc')
            ->limit(5)
            ->get();

        // Growth comparison
        $donationsYesterday = Donation::whereBetween('created_at', [
            $previousDay->copy()->startOfDay(), 
            $previousDay->copy()->endOfDay()
        ])->count();
        $usersYesterday = User::whereBetween('created_at', [
            $previousDay->copy()->startOfDay(), 
            $previousDay->copy()->endOfDay()
        ])->count();

        $donationGrowth = $donationsYesterday > 0 ? (($donationsToday - $donationsYesterday) / $donationsYesterday) * 100 : 0;
        $userGrowth = $usersYesterday > 0 ? (($newUsers - $usersYesterday) / $usersYesterday) * 100 : 0;

        // Recent issues (placeholder - you can implement actual issue detection)
        $recentIssues = [];
        if ($successRate < 80) {
            $recentIssues[] = "Tingkat keberhasilan donasi rendah: " . number_format($successRate, 1) . "%";
        }
        if ($donationsToday == 0) {
            $recentIssues[] = "Tidak ada donasi yang masuk hari ini";
        }

        return [
            'donations_today' => $donationsToday,
            'successful_donations' => $successfulDonations,
            'total_amount_today' => $totalAmountToday,
            'average_donation' => $averageDonation,
            'success_rate' => $successRate,
            'new_campaigns' => $newCampaigns,
            'completed_campaigns' => $completedCampaigns,
            'active_campaigns' => $activeCampaigns,
            'trending_campaigns' => $trendingCampaigns,
            'new_users' => $newUsers,
            'active_donors' => $activeDonors,
            'active_creators' => $activeCreators,
            'total_users' => $totalUsers,
            'top_campaigns' => $topCampaigns,
            'donation_growth' => $donationGrowth,
            'user_growth' => $userGrowth,
            'recent_issues' => $recentIssues,
        ];
    }
}
