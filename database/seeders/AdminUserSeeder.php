<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if doesn't exist
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@platform.com',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);
    }
}
