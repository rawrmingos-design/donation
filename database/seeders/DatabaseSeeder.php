<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Base data - no dependencies
            UserSeeder::class,              // Creates users (admin, creator, donor)
            CategorySeeder::class,          // Creates categories (independent)
            PaymentProviderSeeder::class,   // Creates payment providers & channels (independent)
            
            // 2. User-dependent data
            FundraiserApplicationSeeder::class, // Depends on: User (donor role)
            DonorSeeder::class,             // Depends on: User (for registered donors)
            
            // 3. Campaign data - depends on users and categories
            CampaignSeeder::class,          // Depends on: User (creator role), Category
            CampaignRealDataSeeder::class,  // Depends on: User (creator role), Category
            
            // 4. Transaction data - depends on campaigns and donors
            DonationSeeder::class,          // Depends on: Campaign, User (donor role), Donor
            SuccessDonationSeeder::class,   // Depends on: Donation (updates existing donations)
            
            // 5. Interactive data - depends on campaigns and users
            CommentSeeder::class,           // Depends on: Campaign, User
            
            // 6. Financial data - depends on campaigns with funds
            WithdrawalSeeder::class,        // Depends on: Campaign (with funds), User (creator & admin)
        ]);
    }
}
