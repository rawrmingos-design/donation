<?php

namespace App\Observers;

use App\Models\FundraiserApplication;
use App\Services\CampaignCacheService;

class FundraiserApplicationObserver
{
    protected $cacheService;

    public function __construct(CampaignCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the FundraiserApplication "created" event.
     */
    public function created(FundraiserApplication $application): void
    {
        $this->cacheService->invalidateFundraiserApplicationCaches($application->user_id);
    }

    /**
     * Handle the FundraiserApplication "updated" event.
     */
    public function updated(FundraiserApplication $application): void
    {
        $this->cacheService->invalidateFundraiserApplicationCaches($application->user_id);
    }

    /**
     * Handle the FundraiserApplication "deleted" event.
     */
    public function deleted(FundraiserApplication $application): void
    {
        $this->cacheService->invalidateFundraiserApplicationCaches($application->user_id);
    }
}
