<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Services\CampaignCacheService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    protected $cacheService;

    public function __construct(CampaignCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index()
    {
        // Get urgent campaigns (nearly full or near deadline)
        $urgentCampaigns = Campaign::with(['category', 'user'])
            ->where('status', 'active')
            ->where(function ($query) {
                // Campaigns that are 80% funded or have less than 7 days left
                $query->whereRaw('(collected_amount / target_amount) >= 0.8')
                      ->orWhere('deadline', '<=', now()->addDays(7));
            })
            ->withCount('donations')
            ->orderByRaw('(collected_amount / target_amount) DESC')
            ->limit(6)
            ->get();

        // Get featured campaigns (recent active campaigns)
        $featuredCampaigns = Campaign::with(['category', 'user'])
            ->where('status', 'active')
            ->withCount('donations')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Get platform statistics
        $stats = [
            'total_campaigns' => Campaign::where('status', 'active')->count(),
            'total_raised' => Campaign::where('status', 'active')->sum('collected_amount'),
            'total_donors' => \App\Models\Donation::distinct('donor_id')->count(),
            'campaigns_funded' => Campaign::whereRaw('collected_amount >= target_amount')->count(),
        ];


        return Inertia::render('Home', [
            'urgentCampaigns' => $urgentCampaigns,
            'featuredCampaigns' => $featuredCampaigns,
            'stats' => $stats,
        ]);
    }
}
