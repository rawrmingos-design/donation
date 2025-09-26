<?php

namespace App\Observers;

use App\Models\Campaign;
use App\Services\CampaignCacheService;

class CampaignObserver
{
    protected $cacheService;

    public function __construct(CampaignCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Campaign "created" event.
     */
    public function created(Campaign $campaign): void
    {
        $this->cacheService->invalidateAllCaches();
        $this->cacheService->invalidateDashboardStats();
        $this->cacheService->invalidateUserDashboard($campaign->user_id);
    }

    /**
     * Handle the Campaign "updated" event.
     */
    public function updated(Campaign $campaign): void
    {
        $this->cacheService->invalidateCampaignDetail($campaign->id);
        $this->cacheService->invalidateCampaignsCache();
        $this->cacheService->invalidateDashboardStats();
        $this->cacheService->invalidateUserDashboard($campaign->user_id);
    }

    /**
     * Handle the Campaign "deleted" event.
     */
    public function deleted(Campaign $campaign): void
    {
        $this->cacheService->invalidateAllCaches();
        $this->cacheService->invalidateDashboardStats();
        $this->cacheService->invalidateUserDashboard($campaign->user_id);
    }

    /**
     * Handle the Campaign "restored" event.
     */
    public function restored(Campaign $campaign): void
    {
        $this->cacheService->invalidateAllCaches();
    }

    /**
     * Handle the Campaign "force deleted" event.
     */
    public function forceDeleted(Campaign $campaign): void
    {
        $this->cacheService->invalidateAllCaches();
    }
}
