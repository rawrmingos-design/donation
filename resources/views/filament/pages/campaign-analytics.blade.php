<x-filament-panels::page>
    @php
        $stats = $this->getStats();
        $topCampaigns = $this->getTopCampaigns();
        $recentDonations = $this->getRecentDonations();
        $campaignsByCategory = $this->getCampaignsByCategory();
        $donationTrends = $this->getDonationTrends();
        $paymentMethodStats = $this->getPaymentMethodStats();
    @endphp

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Campaigns -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Campaigns</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['totalCampaigns']) }}</p>
                    <p class="text-xs text-gray-500">
                        <span class="text-green-600">{{ $stats['activeCampaigns'] }} Active</span> • 
                        <span class="text-blue-600">{{ $stats['completedCampaigns'] }} Completed</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Donations -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Donations</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">Rp {{ number_format($stats['totalDonationAmount']) }}</p>
                    <p class="text-xs text-gray-500">{{ number_format($stats['totalDonations']) }} transactions</p>
                </div>
            </div>
        </div>

        <!-- Average Donation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Donation</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">Rp {{ number_format($stats['avgDonationAmount']) }}</p>
                    <p class="text-xs text-gray-500">per transaction</p>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Success Rate</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['successRate'], 1) }}%</p>
                    <p class="text-xs {{ $stats['monthlyGrowth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $stats['monthlyGrowth'] >= 0 ? '+' : '' }}{{ number_format($stats['monthlyGrowth'], 1) }}% this month
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Performing Campaigns -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Top Performing Campaigns</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($topCampaigns as $campaign)
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $campaign['title'] }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $campaign['donation_count'] }} donations
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-green-600">
                                Rp {{ number_format($campaign['total_raised']) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ number_format(($campaign['total_raised'] / max($campaign['target_amount'], 1)) * 100, 1) }}% of target
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Donations</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recentDonations as $donation)
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $donation['donor']['name'] ?? 'Anonymous' }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">
                                {{ $donation['campaign']['title'] }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-green-600">
                                Rp {{ number_format($donation['amount']) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($donation['created_at'])->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Categories Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Performance by Category</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($campaignsByCategory as $category)
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $category['category_name'] ?? 'Uncategorized' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $category['campaign_count'] }} campaigns
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-green-600">
                                Rp {{ number_format($category['total_raised']) }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment Methods</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($paymentMethodStats as $method)
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $method['provider_name'] }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ number_format($method['transaction_count']) }} transactions
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-green-600">
                                Rp {{ number_format($method['total_amount']) }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Donation Trends Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Donation Trends (Last 30 Days)</h3>
        </div>
        <div class="p-6">
            <div class="h-64 flex items-end justify-between space-x-1">
                @foreach($donationTrends as $trend)
                @php
                    $maxAmount = collect($donationTrends)->max('amount');
                    $height = $maxAmount > 0 ? ($trend['amount'] / $maxAmount) * 100 : 0;
                @endphp
                <div class="flex-1 flex flex-col items-center">
                    <div 
                        class="w-full bg-blue-500 rounded-t transition-all duration-300 hover:bg-blue-600"
                        style="height: {{ $height }}%"
                        title="Rp {{ number_format($trend['amount']) }} ({{ $trend['count'] }} donations)"
                    ></div>
                    <span class="text-xs text-gray-500 mt-2 transform -rotate-45 origin-top-left">
                        {{ \Carbon\Carbon::parse($trend['date'])->format('m/d') }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
