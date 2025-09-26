<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@platform.com',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // User::create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@donation.com',
        //     'phone' => '081234567890',
        //     'password' => Hash::make('password'),
        //     'role' => 'admin',
        //     'is_verified' => true,
        //     'email_verified_at' => now(),
        // ]);

        // // Create campaign creator
        // User::create([
        //     'name' => 'Campaign Creator',
        //     'email' => 'creator@donation.com',
        //     'phone' => '081234567891',
        //     'password' => Hash::make('password'),
        //     'role' => 'creator',
        //     'is_verified' => true,
        //     'email_verified_at' => now(),
        // ]);

        // Create donor users
        $donors = [
            [
                'name' => 'Ahmad Rizki Pratama',
                'email' => 'ahmad.rizki@gmail.com',
                'phone' => '081234567892',
                'is_verified' => true,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@yahoo.com',
                'phone' => '082345678901',
                'is_verified' => true,
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'phone' => '083456789012',
                'is_verified' => true,
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@hotmail.com',
                'phone' => '084567890123',
                'is_verified' => true,
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi.wijaya@gmail.com',
                'phone' => '085678901234',
                'is_verified' => false,
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya.sari@yahoo.com',
                'phone' => '086789012345',
                'is_verified' => true,
            ],
            [
                'name' => 'Rudi Hermawan',
                'email' => 'rudi.hermawan@gmail.com',
                'phone' => '087890123456',
                'is_verified' => true,
            ],
            [
                'name' => 'Indah Permata',
                'email' => 'indah.permata@gmail.com',
                'phone' => '088901234567',
                'is_verified' => false,
            ],
            [
                'name' => 'Fajar Nugroho',
                'email' => 'fajar.nugroho@yahoo.com',
                'phone' => '089012345678',
                'is_verified' => true,
            ],
            [
                'name' => 'Rina Kartika',
                'email' => 'rina.kartika@hotmail.com',
                'phone' => '081123456789',
                'is_verified' => true,
            ],
            [
                'name' => 'Hendra Kusuma',
                'email' => 'hendra.kusuma@gmail.com',
                'phone' => '082234567890',
                'is_verified' => false,
            ],
            [
                'name' => 'Lina Marlina',
                'email' => 'lina.marlina@yahoo.com',
                'phone' => '083345678901',
                'is_verified' => true,
            ],
            [
                'name' => 'Doni Setiawan',
                'email' => 'doni.setiawan@gmail.com',
                'phone' => '084456789012',
                'is_verified' => true,
            ],
            [
                'name' => 'Wati Suryani',
                'email' => 'wati.suryani@hotmail.com',
                'phone' => '085567890123',
                'is_verified' => false,
            ],
            [
                'name' => 'Agus Salim',
                'email' => 'agus.salim@gmail.com',
                'phone' => '086678901234',
                'is_verified' => true,
            ],
            [
                'name' => 'Putri Ayu',
                'email' => 'putri.ayu@gmail.com',
                'phone' => '087789012345',
                'is_verified' => true,
            ],
            [
                'name' => 'Bayu Setiawan',
                'email' => 'bayu.setiawan@yahoo.com',
                'phone' => '088890123456',
                'is_verified' => false,
            ],
            [
                'name' => 'Sari Dewi',
                'email' => 'sari.dewi@hotmail.com',
                'phone' => '089901234567',
                'is_verified' => true,
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@gmail.com',
                'phone' => '081012345678',
                'is_verified' => true,
            ],
            [
                'name' => 'Nina Sartika',
                'email' => 'nina.sartika@yahoo.com',
                'phone' => '082123456789',
                'is_verified' => false,
            ],
        ];

        foreach ($donors as $donor) {
            User::create([
                'name' => $donor['name'],
                'email' => $donor['email'],
                'phone' => $donor['phone'],
                'password' => Hash::make('password'),
                'role' => 'donor',
                'is_verified' => $donor['is_verified'],
                'email_verified_at' => $donor['is_verified'] ? now()->subDays(rand(1, 30)) : null,
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Created ' . User::count() . ' users total');
        $this->command->info('Donors: ' . User::where('role', 'donor')->count());
        $this->command->info('Verified users: ' . User::where('is_verified', true)->count());
    }
}
