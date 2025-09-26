<?php

namespace Database\Seeders;

use App\Models\FundraiserApplication;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FundraiserApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get donor users to create applications for
        $donors = User::where('role', 'donor')->take(10)->get();
        
        if ($donors->isEmpty()) {
            $this->command->warn('No donor users found. Creating some donors first...');
            
            // Create some donor users
            for ($i = 1; $i <= 5; $i++) {
                User::create([
                    'name' => "Donor User {$i}",
                    'email' => "donor{$i}@example.com",
                    'password' => bcrypt('password'),
                    'role' => 'donor',
                    'is_active' => true,
                    'is_verified' => true,
                    'phone' => '08' . rand(1000000000, 9999999999),
                ]);
            }
            
            $donors = User::where('role', 'donor')->take(10)->get();
        }

        $motivations = [
            'I have always been passionate about helping others and making a positive impact in my community. Through fundraising, I believe I can connect generous donors with meaningful causes that truly need support.',
            'Having experienced financial hardship myself, I understand the struggles many people face. I want to use my skills and network to help raise funds for those who need it most.',
            'I have been volunteering for various charitable organizations for over 5 years and have seen firsthand how fundraising can transform lives. I want to take my commitment to the next level.',
            'As someone who has benefited from community support in the past, I feel called to give back by helping others access the resources they need through effective fundraising campaigns.',
            'I believe in the power of collective action and want to be a bridge between those who can give and those who need help. Fundraising allows me to make this vision a reality.',
        ];

        $experiences = [
            'I have organized several charity events for my local mosque, raising over $10,000 for disaster relief efforts. I also have experience in social media marketing and community outreach.',
            'I worked as a volunteer coordinator for a local NGO for 2 years, where I helped organize fundraising events and managed donor relationships.',
            'I have a background in marketing and have successfully run crowdfunding campaigns for small businesses. I want to use these skills for charitable causes.',
            'I have been organizing community events and charity drives in my neighborhood for the past 3 years, with a focus on education and healthcare initiatives.',
            'I have experience in event management and have helped organize several charity auctions and benefit dinners that raised significant funds for various causes.',
        ];

        $socialMediaLinks = [
            'https://instagram.com/fundraiser1',
            'https://facebook.com/fundraiser.profile',
            'https://twitter.com/fundraiser_id',
            'https://linkedin.com/in/fundraiser-profile',
            'https://tiktok.com/@fundraiser_official',
        ];

        foreach ($donors as $index => $donor) {
            $status = ['pending', 'approved', 'rejected'][rand(0, 2)];
            $reviewedAt = null;
            $reviewedBy = null;
            $adminNotes = null;

            // If not pending, set review details
            if ($status !== 'pending') {
                $reviewedAt = now()->subDays(rand(1, 30));
                $reviewedBy = User::where('role', 'admin')->first()?->id;
                
                if ($status === 'approved') {
                    $adminNotes = 'Application approved. Candidate shows strong commitment to helping others and has relevant experience.';
                    // Update user role to creator if approved
                    $donor->update(['role' => 'creator']);
                } else {
                    $adminNotes = 'Application rejected. Need more detailed experience and stronger motivation statement.';
                }
            }

            FundraiserApplication::create([
                'user_id' => $donor->id,
                'full_name' => $donor->name,
                'phone' => $donor->phone ?? '08' . rand(1000000000, 9999999999),
                'address' => $this->generateAddress(),
                'id_card_number' => $this->generateIdCardNumber(),
                'id_card_photo' => null, // We'll skip file uploads for seeding
                'motivation' => $motivations[array_rand($motivations)],
                'experience' => rand(0, 1) ? $experiences[array_rand($experiences)] : null,
                'social_media_links' => rand(0, 1) ? $socialMediaLinks[array_rand($socialMediaLinks)] : null,
                'status' => $status,
                'admin_notes' => $adminNotes,
                'reviewed_at' => $reviewedAt,
                'reviewed_by' => $reviewedBy,
                'created_at' => now()->subDays(rand(1, 60)),
            ]);
        }

        $this->command->info('Created ' . $donors->count() . ' fundraiser applications');
    }

    private function generateAddress(): string
    {
        $streets = [
            'Jl. Merdeka No. 123',
            'Jl. Sudirman No. 456',
            'Jl. Thamrin No. 789',
            'Jl. Gatot Subroto No. 321',
            'Jl. Ahmad Yani No. 654',
            'Jl. Diponegoro No. 987',
            'Jl. Imam Bonjol No. 147',
            'Jl. Veteran No. 258',
            'Jl. Pahlawan No. 369',
            'Jl. Kemerdekaan No. 741',
        ];

        $cities = [
            'Jakarta Pusat, DKI Jakarta',
            'Bandung, Jawa Barat',
            'Surabaya, Jawa Timur',
            'Medan, Sumatera Utara',
            'Semarang, Jawa Tengah',
            'Makassar, Sulawesi Selatan',
            'Palembang, Sumatera Selatan',
            'Tangerang, Banten',
            'Depok, Jawa Barat',
            'Bekasi, Jawa Barat',
        ];

        return $streets[array_rand($streets)] . ', ' . $cities[array_rand($cities)] . ' ' . rand(10000, 99999);
    }

    private function generateIdCardNumber(): string
    {
        // Generate a fake Indonesian ID card number format
        $province = str_pad(rand(11, 99), 2, '0', STR_PAD_LEFT);
        $city = str_pad(rand(01, 99), 2, '0', STR_PAD_LEFT);
        $district = str_pad(rand(01, 99), 2, '0', STR_PAD_LEFT);
        $birthDate = str_pad(rand(01, 31), 2, '0', STR_PAD_LEFT);
        $birthMonth = str_pad(rand(01, 12), 2, '0', STR_PAD_LEFT);
        $birthYear = str_pad(rand(70, 99), 2, '0', STR_PAD_LEFT);
        $serial = str_pad(rand(0001, 9999), 4, '0', STR_PAD_LEFT);

        return $province . $city . $district . $birthDate . $birthMonth . $birthYear . $serial;
    }
}
