<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Services\CampaignCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CampaignController extends Controller
{
    protected $cacheService;

    public function __construct(CampaignCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }


    public function show(Campaign $campaign, Request $request)
    {
        // Get fresh campaign data with real-time calculations using scope
        $campaignData = Campaign::with(['category', 'user'])
            ->where('campaigns.id', $campaign->id)
            ->firstOrFail();
            
        // Log campaign access for analytics
        Log::info('Campaign viewed', [
            'campaign_id' => $campaign->id,
            'campaign_slug' => $campaign->slug,
            'collected_amount' => $campaignData->collected_amount,
            'progress_percentage' => $campaignData->progress_percentage,
            'donors_count' => $campaignData->donors_count,
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip()
        ]);
        
        // Get pagination parameters
        $donationsPage = $request->get('donations_page', 1);
        $commentsPage = $request->get('comments_page', 1);
        $activePage = $request->get('active_page', 1);
        $perPage = 10; // Items per page for each section
        
        // Get paginated donations
        $donations = $campaign->donations()
            ->with(['donor'])
            ->where('status', 'success')
            ->latest()
            ->paginate($perPage, ['*'], 'donations_page', $donationsPage);
        
        // Get paginated comments
        $comments = $campaign->comments()
            ->with(['user'])
            ->latest()
            ->paginate($perPage, ['*'], 'comments_page', $commentsPage);
        
        return Inertia::render('Campaigns/Show', [
            'activePage' => $activePage,
            'campaign' => $campaignData,
            'donations' => $donations,
            'comments' => $comments,
        ]);
    }

    public function explore(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'category' => $request->get('category'),
            'status' => $request->get('status'),
        ];

        $page = $request->get('page', 1);
        $perPage = 12; // Items per page for campaigns grid

        // Get campaigns with proper pagination
        $campaignsQuery = Campaign::query()
            ->with(['category', 'user'])
            ->where('status', 'active')
            ->latest();

        // Apply filters
        if ($filters['search']) {
            $campaignsQuery->where(function($query) use ($filters) {
                $query->where('title', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('short_desc', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['category'] && $filters['category'] !== 'all') {
            $campaignsQuery->where('category_id', $filters['category']);
        }

        if ($filters['status'] && $filters['status'] !== 'all') {
            $campaignsQuery->where('status', $filters['status']);
        }

        $campaigns = $campaignsQuery->paginate($perPage, ['*'], 'page', $page);
        $categories = $this->cacheService->getCategories();

        return Inertia::render('Campaigns/Explore', [
            'campaigns' => $campaigns,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $categories = $this->cacheService->getCategories();

        return Inertia::render('Campaigns/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:300',
            'category_id' => 'required|exists:categories,id',
            'short_desc' => 'required|string|max:500',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:100000',
            'deadline' => 'required|date|after:today',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        $validated['status'] = 'draft';

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('campaigns', 'public');
        }

        $campaign = Campaign::create($validated);

        // Invalidate cache after creating new campaign
        $this->cacheService->invalidateAllCaches();

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign created successfully!');
    }

    public function edit(Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        
        $categories = $this->cacheService->getCategories();

        return Inertia::render('Campaigns/Edit', [
            'campaign' => $campaign,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $validated = $request->validate([
            'title' => 'required|string|max:300',
            'category_id' => 'required|exists:categories,id',
            'short_desc' => 'required|string|max:500',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:100000',
            'deadline' => 'required|date|after:today',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('campaigns', 'public');
        }

        $campaign->update($validated);

        // Invalidate cache after updating campaign
        $this->cacheService->invalidateCampaignDetail($campaign->id);
        $this->cacheService->invalidateCampaignsCache();

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign updated successfully!');
    }

    public function destroy(Campaign $campaign)
    {
        $this->authorize('delete', $campaign);
        
        $campaign->delete();

        // Invalidate cache after deleting campaign
        $this->cacheService->invalidateAllCaches();

        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted successfully!');
    }
}
