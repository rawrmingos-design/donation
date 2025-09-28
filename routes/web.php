<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\FundraiserApplicationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CampaignShareController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

// About page
Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

// Contact page
Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

// Terms of Service page
Route::get('/terms-of-service', function () {
    return Inertia::render('TermsOfService');
})->name('terms-of-service');

// Privacy Policy page
Route::get('/privacy-policy', function () {
    return Inertia::render('PrivacyPolicy');
})->name('privacy-policy');

// How It Works page
Route::get('/how-it-works', function () {
    return Inertia::render('HowItWorks');
})->name('how-it-works');

// FAQ page
Route::get('/faq', function () {
    return Inertia::render('FAQ');
})->name('faq');

// Public campaign routes
Route::get('/campaigns', [CampaignController::class, 'explore'])->name('campaigns.index');
Route::get('/campaigns/{campaign:slug}', [CampaignController::class, 'show'])->name('campaigns.show');

// Donation routes with rate limiting
Route::get('/campaigns/{campaign:slug}/donate', [DonationController::class, 'create'])->name('donations.create');
Route::post('/campaigns/{campaign:slug}/donate', [DonationController::class, 'store'])->name('donations.store')->middleware('rate.limit:donation,10,2');
Route::get('/donations/{transaction:ref_id}/show', [DonationController::class, 'show'])->name('donations.show');
Route::get('/donations/{transaction:ref_id}/success', [DonationController::class, 'success'])->name('donations.success');

// Comment routes
Route::middleware('auth')->group(function () {
    Route::post('/campaigns/{campaign:slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// OAuth routes
Route::prefix('auth')->group(function () {
    // Google OAuth
    Route::get('/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    
    // Facebook OAuth
    Route::get('/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
    Route::get('/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback'])->name('auth.facebook.callback');
});

// Webhook route (no auth required)
Route::post('/webhook/tokopay', [DonationController::class, 'webhook'])->name('webhook.tokopay');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard route - only for donor and creator roles
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware('role:donor:creator')
        ->name('dashboard');

    // Campaign management routes - only for creator role with rate limiting and file upload security
    Route::middleware(['resource.role:campaigns:creator', 'secure.upload'])->group(function () {
        Route::get('/campaign/create', [CampaignController::class, 'create'])->name('campaigns.create');
        Route::post('/campaign', [CampaignController::class, 'store'])->name('campaigns.store')->middleware('rate.limit:campaign,5,10');
        Route::get('/campaign/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
        Route::put('/campaign/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update')->middleware('rate.limit:campaign,10,10');
        Route::delete('/campaign/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
    });

    // Fundraiser application routes - only for donor role (to apply as fundraiser)
    Route::middleware('resource.role:fundraiser:donor')->group(function () {
        Route::get('/fundraiser/application', [FundraiserApplicationController::class, 'index'])->name('fundraiser.application');
        Route::post('/fundraiser/application', [FundraiserApplicationController::class, 'store'])->name('fundraiser.application.store');
        Route::put('/fundraiser/application/{application}', [FundraiserApplicationController::class, 'update'])->name('fundraiser.application.update');
    });

    // Withdrawal routes - only for creator role
    Route::middleware('resource.role:withdrawals:creator')->group(function () {
        Route::get('/campaigns/{campaign}/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::get('/campaigns/{campaign}/withdrawals/create', [WithdrawalController::class, 'create'])->name('withdrawals.create');
        Route::post('/campaigns/{campaign}/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');
        Route::get('/withdrawals/{withdrawal}', [WithdrawalController::class, 'show'])->name('withdrawals.show');
        Route::patch('/withdrawals/{withdrawal}/cancel', [WithdrawalController::class, 'cancel'])->name('withdrawals.cancel');
    });

    // Notification page
    Route::get('/notifications', function () {
        return Inertia::render('Notifications/Index');
    })->name('notifications.page');

    // Notification API routes
    Route::prefix('api/notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
        Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });

});

// Campaign Share API routes - outside auth middleware to avoid Inertia conflicts
Route::prefix('api/campaigns')->middleware(['web', 'rate.limit:share,30,1'])->group(function () {
    Route::post('/share-track', [CampaignShareController::class, 'trackShare'])->name('campaigns.share.track');
    Route::get('/{campaign}/share-stats', [CampaignShareController::class, 'getShareStats'])->name('campaigns.share.stats');
    Route::get('/share-stats/global', [CampaignShareController::class, 'getGlobalStats'])->name('campaigns.share.global');
});

// Private file serving route
Route::middleware(['auth'])->get('/storage/id-cards/{filename}', function ($filename) {
    $path = storage_path('app/id-cards/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
})->name('fundraiser.id-card.view');

// Webhook routes (outside auth middleware)
Route::post('/webhooks/midtrans', [App\Http\Controllers\MidtransWebhookController::class, 'handle'])->name('webhooks.midtrans');

// 404 Not Found page
Route::fallback(function () {
    return Inertia::render('NotFound');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
