<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampaignShareController extends Controller
{
    /**
     * Track a campaign share
     */
    public function trackShare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'campaign_id' => 'required|exists:campaigns,id',
            'platform' => 'required|string|in:facebook,twitter,whatsapp,linkedin,telegram,clipboard,native',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $share = CampaignShare::create([
                'campaign_id' => $request->campaign_id,
                'platform' => $request->platform,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->header('referer'),
                'metadata' => [
                    'timestamp' => now()->toISOString(),
                    'session_id' => session()->getId(),
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Share tracked successfully',
                'share_id' => $share->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to track share',
            ], 500);
        }
    }

    /**
     * Get share statistics for a campaign
     */
    public function getShareStats(Campaign $campaign)
    {
        $sharesByPlatform = CampaignShare::getShareCountByPlatform($campaign->id);
        $totalShares = CampaignShare::getTotalShareCount($campaign->id);

        // Get recent shares (last 7 days)
        $recentShares = $campaign->shares()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // Get daily share trend (last 30 days)
        $dailyTrend = $campaign->shares()
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(fn($item) => $item->count)
            ->toArray();

        return response()->json([
            'total_shares' => $totalShares,
            'shares_by_platform' => $sharesByPlatform,
            'recent_shares' => $recentShares,
            'daily_trend' => $dailyTrend,
            'popular_platforms' => array_keys(array_slice($sharesByPlatform, 0, 3, true)),
        ]);
    }

    /**
     * Get global share statistics
     */
    public function getGlobalStats()
    {
        $totalShares = CampaignShare::count();
        $popularPlatforms = CampaignShare::getPopularPlatforms();
        
        // Most shared campaigns
        $mostSharedCampaigns = Campaign::withCount('shares')
            ->orderByDesc('shares_count')
            ->limit(10)
            ->get(['id', 'title', 'slug'])
            ->map(function ($campaign) {
                return [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'slug' => $campaign->slug,
                    'shares_count' => $campaign->shares_count,
                ];
            });

        return response()->json([
            'total_shares' => $totalShares,
            'popular_platforms' => $popularPlatforms,
            'most_shared_campaigns' => $mostSharedCampaigns,
        ]);
    }
}
