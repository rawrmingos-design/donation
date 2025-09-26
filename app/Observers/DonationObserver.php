<?php

namespace App\Observers;

use App\Models\Donation;
use App\Services\CampaignCacheService;

class DonationObserver
{
    protected $cacheService;

    public function __construct(CampaignCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Donation "created" event.
     */
    public function created(Donation $donation): void
    {
        // Invalidate dashboard stats when new donation is created
        $this->cacheService->invalidateDashboardStats();
        
        // Also invalidate campaign detail cache as collected_amount might change
        $this->cacheService->invalidateCampaignDetail($donation->campaign_id);
        
        // Invalidate user dashboard for donor
        $this->cacheService->invalidateUserDashboard($donation->user_id);
    }

    /**
     * Handle the Donation "updated" event.
     */
    public function updated(Donation $donation): void
    {
        $this->cacheService->invalidateDashboardStats();
        $this->cacheService->invalidateCampaignDetail($donation->campaign_id);
        $this->cacheService->invalidateUserDashboard($donation->user_id);
    }

    /**
     * Handle the Donation "deleted" event.
     */
    public function deleted(Donation $donation): void
    {
        $this->cacheService->invalidateDashboardStats();
        $this->cacheService->invalidateCampaignDetail($donation->campaign_id);
        $this->cacheService->invalidateUserDashboard($donation->user_id);
    }

    /**
     * Handle the Donation "restored" event.
     */
    public function restored(Donation $donation): void
    {
        $this->cacheService->invalidateDashboardStats();
        $this->cacheService->invalidateCampaignDetail($donation->campaign_id);
        $this->cacheService->invalidateUserDashboard($donation->user_id);
    }

    /**
     * Handle the Donation "force deleted" event.
     */
    public function forceDeleted(Donation $donation): void
    {
        $this->cacheService->invalidateDashboardStats();
        $this->cacheService->invalidateCampaignDetail($donation->campaign_id);
        $this->cacheService->invalidateUserDashboard($donation->user_id);
    }
}
