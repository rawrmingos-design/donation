<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CampaignCacheService;

class ClearCampaignCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-campaigns {--type=all : Type of cache to clear (all, campaigns, categories, details, dashboard, user-dashboard)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear campaign and dashboard related cache';

    protected $cacheService;

    public function __construct(CampaignCacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');

        switch ($type) {
            case 'campaigns':
                $this->cacheService->invalidateCampaignsCache();
                $this->info('Campaigns cache cleared successfully.');
                break;

            case 'categories':
                $this->cacheService->invalidateCategoriesCache();
                $this->info('Categories cache cleared successfully.');
                break;

            case 'details':
                $this->cacheService->forgetByPattern('campaign_detail*');
                $this->info('Campaign details cache cleared successfully.');
                break;

            case 'dashboard':
                $this->cacheService->invalidateDashboardStats();
                $this->info('Dashboard stats cache cleared successfully.');
                break;

            case 'user-dashboard':
                $this->cacheService->invalidateAllDashboardCaches();
                $this->info('All user dashboard caches cleared successfully.');
                break;

            case 'all':
            default:
                $this->cacheService->invalidateAllCaches();
                $this->cacheService->invalidateAllDashboardCaches();
                $this->info('All campaign and dashboard caches cleared successfully.');
                break;
        }

        // Show cache statistics
        $stats = $this->cacheService->getCacheStats();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Cache Driver', $stats['driver']],
                ['Campaigns Cached', $stats['campaigns_cached']],
                ['Categories Cached', $stats['categories_cached'] ? 'Yes' : 'No'],
                ['Campaign Details Cached', $stats['campaign_details_cached']],
                ['Dashboard Stats Cached', $stats['dashboard_stats_cached'] ? 'Yes' : 'No'],
                ['User Dashboards Cached', $stats['user_dashboards_cached']],
            ]
        );

        return 0;
    }
}
