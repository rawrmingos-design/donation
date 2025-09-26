<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\FundraiserApplication;
use App\Models\Donation;
use App\Services\CampaignCacheService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $cacheService;

    public function __construct(CampaignCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Get cached stats
        $stats = $this->cacheService->getDashboardStats();

        // Get cached user-specific data
        $data = $this->cacheService->getUserDashboardData($user->id, $user->role);
        
        // Add stats to data
        $data['stats'] = $stats;

        return Inertia::render('dashboard', $data);
    }
}
