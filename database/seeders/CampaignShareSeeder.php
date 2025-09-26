<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignShare;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampaignShareSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('📊 Seeding campaign shares...');

        $campaigns = Campaign::all();
        
        if ($campaigns->isEmpty()) {
            $this->command->warn('No campaigns found. Please seed campaigns first.');
            return;
        }

        $platforms = ['facebook', 'twitter', 'whatsapp', 'linkedin', 'telegram', 'clipboard', 'native'];
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_7_1 like Mac OS X) AppleWebKit/605.1.15',
            'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0 Firefox/88.0',
        ];

        $totalShares = 0;

        foreach ($campaigns as $campaign) {
            // Generate random number of shares for each campaign (0-50)
            $shareCount = rand(0, 50);
            
            for ($i = 0; $i < $shareCount; $i++) {
                $platform = $platforms[array_rand($platforms)];
                $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
                
                CampaignShare::create([
                    'campaign_id' => $campaign->id,
                    'platform' => $platform,
                    'ip_address' => $this->generateRandomIP(),
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'referrer' => $this->generateRandomReferrer(),
                    'metadata' => [
                        'timestamp' => $createdAt->toISOString(),
                        'session_id' => 'seed_' . uniqid(),
                        'campaign_title' => $campaign->title,
                        'share_method' => $platform === 'native' ? 'mobile_native' : 'web_button',
                    ],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
                
                $totalShares++;
            }
            
            $this->command->line("✓ Generated {$shareCount} shares for campaign: {$campaign->title}");
        }

        $this->command->info("✅ Created {$totalShares} campaign shares across " . $campaigns->count() . " campaigns");
        
        // Show some statistics
        $this->showStatistics();
    }

    private function generateRandomIP(): string
    {
        return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }

    private function generateRandomReferrer(): ?string
    {
        $referrers = [
            'https://www.google.com/',
            'https://www.facebook.com/',
            'https://twitter.com/',
            'https://www.instagram.com/',
            'https://wa.me/',
            'direct',
            null,
        ];

        return $referrers[array_rand($referrers)];
    }

    private function showStatistics(): void
    {
        $this->command->newLine();
        $this->command->info('📈 Share Statistics:');
        
        // Total shares
        $totalShares = CampaignShare::count();
        $this->command->line("Total shares: {$totalShares}");
        
        // Shares by platform
        $sharesByPlatform = CampaignShare::selectRaw('platform, count(*) as count')
            ->groupBy('platform')
            ->orderByDesc('count')
            ->get();
            
        $this->command->line('Shares by platform:');
        foreach ($sharesByPlatform as $stat) {
            $this->command->line("  {$stat->platform}: {$stat->count}");
        }
        
        // Most shared campaigns
        $mostShared = Campaign::withCount('shares')
            ->orderByDesc('shares_count')
            ->limit(3)
            ->get();
            
        $this->command->line('Most shared campaigns:');
        foreach ($mostShared as $campaign) {
            $this->command->line("  \"{$campaign->title}\": {$campaign->shares_count} shares");
        }
    }
}
