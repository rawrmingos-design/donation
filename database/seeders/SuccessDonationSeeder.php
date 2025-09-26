<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Donation;

class SuccessDonationSeeder extends Seeder
{
    public function run(): void
    {
        $donations = Donation::all();

        foreach ($donations as $donation) {
            $donation->update([
                'status' => random_int(0, 1) ? 'success' : 'pending',
            ]);
        }
    }
}