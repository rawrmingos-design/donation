<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\Donation;
use App\Models\FundraiserApplication;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CampaignCacheService
{
    const CACHE_TTL = 300; // 5 minutes
    const CAMPAIGNS_CACHE_KEY = 'campaigns';
    const CATEGORIES_CACHE_KEY = 'categories';
    const CAMPAIGN_DETAIL_KEY = 'campaign_detail';
    const DASHBOARD_STATS_KEY = 'dashboard_stats';
    const USER_DASHBOARD_KEY = 'user_dashboard';

    /**
     * Get cached campaigns with filters
     */
    public function getCampaigns(array $filters = [], int $page = 1, int $perPage = 12)
    {
        $cacheKey = $this->generateCampaignsKey($filters, $page, $perPage);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters, $page, $perPage) {
            Log::info('Cache miss for campaigns', ['filters' => $filters, 'page' => $page]);
            
            $query = Campaign::with(['category', 'user'])
                ->selectRaw('campaigns.*, COALESCE(SUM(donations.amount), 0) as collected_amount')
                ->selectRaw('CASE WHEN campaigns.target_amount > 0 THEN ROUND((COALESCE(SUM(donations.amount), 0) / campaigns.target_amount) * 100, 2) ELSE 0 END as progress_percentage')
                ->leftJoin('donations', 'campaigns.id', '=', 'donations.campaign_id')
                ->groupBy('campaigns.id');

            // Apply filters
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('campaigns.title', 'like', "%{$search}%")
                      ->orWhere('campaigns.short_desc', 'like', "%{$search}%")
                      ->orWhere('campaigns.description', 'like', "%{$search}%");
                }); 
            }

            if (!empty($filters['category'])) {
                $query->whereHas('category', function ($q) use ($filters) {
                    $q->where('slug', $filters['category']);
                });
            }

            if (!empty($filters['status'])) {
                $query->where('campaigns.status', $filters['status']);
            } else {
                $query->where('campaigns.status', 'active');
            }

            return $query->latest('campaigns.created_at')->paginate($perPage, ['*'], 'page', $page)->withQueryString();
        });
    }

    /**
     * Get cached categories
     */
    public function getCategories()
    {
        return Cache::remember(self::CATEGORIES_CACHE_KEY, self::CACHE_TTL, function () {
            Log::info('Cache miss for categories');
            return Category::all();
        });
    }

    /**
     * Get cached campaign detail
     */
    public function getCampaignDetail($campaignId)
    {
        $cacheKey = self::CAMPAIGN_DETAIL_KEY . "_{$campaignId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($campaignId) {
            Log::info('Cache miss for campaign detail', ['id' => $campaignId]);
            
            return Campaign::with([
                'category',
                'user',
                'donations.donor',
                'campaignUpdates' => fn($query) => $query->latest(),
                'comments' => fn($query) => $query->where('is_public', true)->with('user')->latest(),
            ])
            ->selectRaw('campaigns.*, COALESCE(SUM(donations.amount), 0) as collected_amount')
            ->selectRaw('CASE WHEN campaigns.target_amount > 0 THEN ROUND((COALESCE(SUM(donations.amount), 0) / campaigns.target_amount) * 100, 2) ELSE 0 END as progress_percentage')
            ->leftJoin('donations', 'campaigns.id', '=', 'donations.campaign_id')
            ->where('campaigns.id', $campaignId)
            ->groupBy('campaigns.id')
            ->firstOrFail();
        });
    }

    /**
     * Invalidate campaigns cache
     */
    public function invalidateCampaignsCache()
    {
        $pattern = self::CAMPAIGNS_CACHE_KEY . '*';
        $this->forgetByPattern($pattern);
        Log::info('Invalidated campaigns cache');
    }

    /**
     * Invalidate categories cache
     */
    public function invalidateCategoriesCache()
    {
        Cache::forget(self::CATEGORIES_CACHE_KEY);
        Log::info('Invalidated categories cache');
    }

    /**
     * Invalidate specific campaign detail cache
     */
    public function invalidateCampaignDetail($campaignId)
    {
        $cacheKey = self::CAMPAIGN_DETAIL_KEY . "_{$campaignId}";
        Cache::forget($cacheKey);
        Log::info('Invalidated campaign detail cache', ['id' => $campaignId]);
    }

    /**
     * Invalidate all campaign-related caches
     */
    public function invalidateAllCaches()
    {
        $this->invalidateCampaignsCache();
        $this->invalidateCategoriesCache();
        $this->forgetByPattern(self::CAMPAIGN_DETAIL_KEY . '*');
        Log::info('Invalidated all campaign caches');
    }

    /**
     * Generate cache key for campaigns
     */
    private function generateCampaignsKey(array $filters, int $page, int $perPage): string
    {
        $filterString = http_build_query($filters);
        return self::CAMPAIGNS_CACHE_KEY . "_{$filterString}_page_{$page}_per_{$perPage}";
    }

    /**
     * Forget cache by pattern (Redis specific)
     */
    private function forgetByPattern(string $pattern)
    {
        if (config('cache.default') === 'redis') {
            $redis = Cache::getRedis();
            $keys = $redis->keys($pattern);
            if (!empty($keys)) {
                $redis->del($keys);
            }
        } else {
            // Fallback for non-Redis cache drivers
            Log::warning('Pattern-based cache invalidation only works with Redis');
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        return [
            'driver' => config('cache.default'),
            'campaigns_cached' => $this->countCachedKeys(self::CAMPAIGNS_CACHE_KEY),
            'categories_cached' => Cache::has(self::CATEGORIES_CACHE_KEY),
            'campaign_details_cached' => $this->countCachedKeys(self::CAMPAIGN_DETAIL_KEY),
            'dashboard_stats_cached' => Cache::has(self::DASHBOARD_STATS_KEY),
            'user_dashboards_cached' => $this->countCachedKeys(self::USER_DASHBOARD_KEY),
        ];
    }

    /**
     * Get cached dashboard stats
     */
    public function getDashboardStats()
    {
        return Cache::remember(self::DASHBOARD_STATS_KEY, self::CACHE_TTL, function () {
            Log::info('Cache miss for dashboard stats');
            
            return [
                'totalCampaigns' => Campaign::count(),
                'totalDonations' => Donation::count(),
                'totalAmount' => Donation::sum('amount'),
                'activeCampaigns' => Campaign::where('status', 'active')->count(),
            ];
        });
    }

    /**
     * Get cached user-specific dashboard data
     */
    public function getUserDashboardData($userId, $userRole)
    {
        $cacheKey = self::USER_DASHBOARD_KEY . "_{$userId}_{$userRole}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId, $userRole) {
            Log::info('Cache miss for user dashboard', ['user_id' => $userId, 'role' => $userRole]);
            
            $data = [];
            
            switch ($userRole) {
                case 'donor':
                    $data['fundraiserApplications'] = FundraiserApplication::where('user_id', $userId)
                        ->with('user')
                        ->orderBy('created_at', 'desc')
                        ->get();
                    break;

                case 'creator':
                    $data['campaigns'] = Campaign::where('campaigns.user_id', $userId)
                        ->with(['category', 'user'])
                        ->selectRaw('campaigns.*, COALESCE(SUM(donations.amount), 0) as collected_amount')
                        ->selectRaw('CASE WHEN campaigns.target_amount > 0 THEN ROUND((COALESCE(SUM(donations.amount), 0) / campaigns.target_amount) * 100, 2) ELSE 0 END as progress_percentage')
                        ->leftJoin('donations', 'campaigns.id', '=', 'donations.campaign_id')
                        ->groupBy('campaigns.id')
                        ->orderBy('campaigns.created_at', 'desc')
                        ->get();
                    
                    // Creator can also see their fundraiser application history
                    $data['fundraiserApplications'] = FundraiserApplication::where('user_id', $userId)
                        ->with('user')
                        ->orderBy('created_at', 'desc')
                        ->get();
                    break;
            }

            return $data;
        });
    }

    /**
     * Invalidate dashboard stats cache
     */
    public function invalidateDashboardStats()
    {
        Cache::forget(self::DASHBOARD_STATS_KEY);
        Log::info('Invalidated dashboard stats cache');
    }

    /**
     * Invalidate user dashboard cache
     */
    public function invalidateUserDashboard($userId, $userRole = null)
    {
        if ($userRole) {
            $cacheKey = self::USER_DASHBOARD_KEY . "_{$userId}_{$userRole}";
            Cache::forget($cacheKey);
            Log::info('Invalidated user dashboard cache', ['user_id' => $userId, 'role' => $userRole]);
        } else {
            // Invalidate all user dashboard caches for this user
            $this->forgetByPattern(self::USER_DASHBOARD_KEY . "_{$userId}_*");
            Log::info('Invalidated all user dashboard caches', ['user_id' => $userId]);
        }
    }

    /**
     * Invalidate all dashboard caches
     */
    public function invalidateAllDashboardCaches()
    {
        $this->invalidateDashboardStats();
        $this->forgetByPattern(self::USER_DASHBOARD_KEY . '*');
        Log::info('Invalidated all dashboard caches');
    }

    /**
     * Invalidate fundraiser application related caches
     */
    public function invalidateFundraiserApplicationCaches($userId = null)
    {
        if ($userId) {
            // Clear specific user caches for both donor and creator roles
            $this->invalidateUserDashboard($userId, 'donor');
            $this->invalidateUserDashboard($userId, 'creator');
        }
        
        // Clear dashboard stats
        $this->invalidateDashboardStats();
        
        Log::info('Invalidated fundraiser application caches', ['user_id' => $userId]);
    }

    /**
     * Count cached keys by pattern
     */
    private function countCachedKeys(string $pattern): int
    {
        if (config('cache.default') === 'redis') {
            $redis = Cache::getRedis();
            return count($redis->keys($pattern . '*'));
        }
        return 0;
    }
}
