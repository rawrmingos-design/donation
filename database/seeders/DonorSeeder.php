<?php

namespace Database\Seeders;

use App\Models\Donor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DonorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $users = User::all();

        // Create donors with registered users (70% of donors)
        $registeredDonors = [
            [
                'name' => 'Ahmad Rizki Pratama',
                'email' => 'ahmad.rizki@gmail.com',
                'phone' => '081234567890',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@yahoo.com',
                'phone' => '082345678901',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'phone' => '083456789012',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@hotmail.com',
                'phone' => '084567890123',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi.wijaya@gmail.com',
                'phone' => '085678901234',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya.sari@yahoo.com',
                'phone' => '086789012345',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Rudi Hermawan',
                'email' => 'rudi.hermawan@gmail.com',
                'phone' => '087890123456',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Indah Permata',
                'email' => 'indah.permata@gmail.com',
                'phone' => '088901234567',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Fajar Nugroho',
                'email' => 'fajar.nugroho@yahoo.com',
                'phone' => '089012345678',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Rina Kartika',
                'email' => 'rina.kartika@hotmail.com',
                'phone' => '081123456789',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Hendra Kusuma',
                'email' => 'hendra.kusuma@gmail.com',
                'phone' => '082234567890',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Lina Marlina',
                'email' => 'lina.marlina@yahoo.com',
                'phone' => '083345678901',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Doni Setiawan',
                'email' => 'doni.setiawan@gmail.com',
                'phone' => '084456789012',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Wati Suryani',
                'email' => 'wati.suryani@hotmail.com',
                'phone' => '085567890123',
                'is_anonymous' => false,
            ],
            [
                'name' => 'Agus Salim',
                'email' => 'agus.salim@gmail.com',
                'phone' => '086678901234',
                'is_anonymous' => false,
            ],
        ];

        // Create anonymous donors (30% of donors)
        $anonymousDonors = [
            [
                'name' => 'Donatur Anonim',
                'email' => 'anonymous1@example.com',
                'phone' => null,
                'is_anonymous' => true,
            ],
            [
                'name' => 'Hamba Allah',
                'email' => 'anonymous2@example.com',
                'phone' => null,
                'is_anonymous' => true,
            ],
            [
                'name' => 'Donatur Baik Hati',
                'email' => 'anonymous3@example.com',
                'phone' => null,
                'is_anonymous' => true,
            ],
            [
                'name' => 'Sahabat Peduli',
                'email' => 'anonymous4@example.com',
                'phone' => null,
                'is_anonymous' => true,
            ],
            [
                'name' => 'Dermawan',
                'email' => 'anonymous5@example.com',
                'phone' => null,
                'is_anonymous' => true,
            ],
            [
                'name' => 'Orang Baik',
                'email' => 'anonymous6@example.com',
                'phone' => null,
                'is_anonymous' => true,
            ],
            [
                'name' => 'Penolong Sesama',
                'email' => 'anonymous7@example.com',
                'phone' => null,
                'is_anonymous' => true,
            ],
        ];

        // Create registered donors (with user_id)
        foreach ($registeredDonors as $donorData) {
            $user = $users->isNotEmpty() ? $users->random() : null;
            
            Donor::create([
                'user_id' => $user ? $user->id : null,
                'name' => $donorData['name'],
                'email' => $donorData['email'],
                'phone' => $donorData['phone'],
                'is_anonymous' => $donorData['is_anonymous'],
                'created_at' => now()->subDays(rand(0, 60)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // Create anonymous donors (without user_id)
        foreach ($anonymousDonors as $donorData) {
            Donor::create([
                'user_id' => null,
                'name' => $donorData['name'],
                'email' => $donorData['email'],
                'phone' => $donorData['phone'],
                'is_anonymous' => $donorData['is_anonymous'],
                'created_at' => now()->subDays(rand(0, 60)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // Create additional random donors using Faker
        for ($i = 0; $i < 30; $i++) {
            $isAnonymous = $faker->boolean(30); // 30% chance to be anonymous
            $hasUser = !$isAnonymous && $users->isNotEmpty() && $faker->boolean(70); // 70% of non-anonymous have user accounts
            
            Donor::create([
                'user_id' => $hasUser ? $users->random()->id : null,
                'name' => $isAnonymous ? 'Donatur Anonim' : $faker->name,
                'email' => $isAnonymous ? $faker->unique()->safeEmail : $faker->unique()->safeEmail,
                'phone' => $isAnonymous ? null : $faker->optional(0.8)->phoneNumber,
                'is_anonymous' => $isAnonymous,
                'created_at' => $faker->dateTimeBetween('-90 days', 'now'),
                'updated_at' => $faker->dateTimeBetween('-30 days', 'now'),
            ]);
        }

        $this->command->info('Donors seeded successfully!');
        $this->command->info('Created ' . Donor::count() . ' donors total');
        $this->command->info('Anonymous donors: ' . Donor::where('is_anonymous', true)->count());
        $this->command->info('Registered donors: ' . Donor::where('is_anonymous', false)->count());
    }
}
