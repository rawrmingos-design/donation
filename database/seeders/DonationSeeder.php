<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campaigns = Campaign::all();
        $users = User::where('role', 'donor')->get();

        if ($campaigns->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Please run CampaignSeeder and UserSeeder first.');
            return;
        }

        // Create donor records from users (or get existing ones)
        $donors = collect();
        foreach ($users as $user) {
            // Check if donor already exists for this user
            $donor = Donor::where('user_id', $user->id)->first();
            
            if (!$donor) {
                $donor = Donor::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'is_anonymous' => rand(0, 10) == 0, // 10% chance of anonymous
                ]);
            }
            $donors->push($donor);
        }

        if ($donors->isEmpty()) {
            $this->command->warn('No donors available. Please check UserSeeder.');
            return;
        }

        $donationMessages = [
            'Hope this helps! Keep up the great work.',
            'Praying for success in this noble cause.',
            'Thank you for making a difference in our community.',
            'Every little bit counts. God bless!',
            'Supporting this amazing initiative.',
            'Keep fighting the good fight!',
            'Proud to be part of this cause.',
            'May this campaign reach its goal soon.',
            'Sending love and support.',
            'Together we can make a change.',
            null, // Some donations without messages
        ];

        // Create donations for each campaign
        $this->command->info("Creating donations for {$campaigns->count()} campaigns using {$donors->count()} donors...");
        
        foreach ($campaigns as $campaign) {
            $donationCount = rand(5, 15); // Reduced to avoid too many donations
            
            for ($i = 0; $i < $donationCount; $i++) {
                $donor = $donors->random();
                $amount = $this->generateDonationAmount();
                
                try {
                    Donation::create([
                        'campaign_id' => $campaign->id,
                        'donor_id' => $donor->id,
                        'amount' => $amount,
                        'currency' => 'IDR',
                        'message' => $donationMessages[array_rand($donationMessages)],
                        'created_at' => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                        'updated_at' => now()->subDays(rand(0, 5)),
                    ]);

                    $campaign->update([
                        'collected_amount' => $campaign->collected_amount + $amount,
                        'donors_count' => $campaign->donors_count + 1,
                    ]);
                } catch (\Exception $e) {
                    $this->command->error("Failed to create donation for campaign {$campaign->id} with donor {$donor->id}: " . $e->getMessage());
                    continue;
                }
            }
        }

        $this->command->info('Donation seeder completed successfully.');
    }

    /**
     * Generate realistic donation amounts
     */
    private function generateDonationAmount(): int
    {
        $amounts = [
            50000,   // 50k IDR
            100000,  // 100k IDR
            200000,  // 200k IDR
            500000,  // 500k IDR
            1000000, // 1M IDR
            2000000, // 2M IDR
            5000000, // 5M IDR
        ];

        // Weight smaller amounts more heavily
        $weights = [30, 25, 20, 15, 7, 2, 1];
        
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        for ($i = 0; $i < count($amounts); $i++) {
            $currentWeight += $weights[$i];
            if ($random <= $currentWeight) {
                return $amounts[$i];
            }
        }
        
        return $amounts[0]; // Fallback
    }
}
