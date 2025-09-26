<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\CampaignCacheService;

class CategoryObserver
{
    protected $cacheService;

    public function __construct(CampaignCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        $this->cacheService->invalidateCategoriesCache();
        $this->cacheService->invalidateCampaignsCache();
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        $this->cacheService->invalidateCategoriesCache();
        $this->cacheService->invalidateCampaignsCache();
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        $this->cacheService->invalidateAllCaches();
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        $this->cacheService->invalidateAllCaches();
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        $this->cacheService->invalidateAllCaches();
    }
}
