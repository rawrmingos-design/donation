<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Services\CampaignCacheService;
use App\Http\Controllers\CampaignShareController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/categories', function (CampaignCacheService $cacheService) {
    return response()->json([
        'data' => $cacheService->getCategories()
    ]);
});

Route::get('/campaigns/stats', function (CampaignCacheService $cacheService) {
    return response()->json([
        'cache_stats' => $cacheService->getCacheStats()
    ]);
});

