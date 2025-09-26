<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Donation;
use App\Models\FundraiserApplication;
use App\Models\Transaction;
use App\Observers\CampaignObserver;
use App\Observers\CategoryObserver;
use App\Observers\DonationObserver;
use App\Observers\FundraiserApplicationObserver;
use App\Observers\TransactionObserver;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Campaign::observe(CampaignObserver::class);
        Category::observe(CategoryObserver::class);
        Donation::observe(DonationObserver::class);
        FundraiserApplication::observe(FundraiserApplicationObserver::class);
        Transaction::observe(TransactionObserver::class);
    }
}
