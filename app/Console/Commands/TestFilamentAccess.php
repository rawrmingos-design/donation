<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestFilamentAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filament:test-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Filament admin access control by creating test users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test users for Filament access control...');

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'is_verified' => true,
            ]
        );

        // Create creator user
        $creator = User::firstOrCreate(
            ['email' => 'creator@test.com'],
            [
                'name' => 'Test Creator',
                'password' => Hash::make('password'),
                'role' => 'creator',
                'is_active' => true,
                'is_verified' => true,
            ]
        );

        // Create donor user
        $donor = User::firstOrCreate(
            ['email' => 'donor@test.com'],
            [
                'name' => 'Test Donor',
                'password' => Hash::make('password'),
                'role' => 'donor',
                'is_active' => true,
                'is_verified' => true,
            ]
        );

        $this->info('Test users created successfully!');
        $this->line('');
        $this->info('Test Credentials:');
        $this->line('Admin: admin@test.com / password (CAN access /admin)');
        $this->line('Creator: creator@test.com / password (CAN access /admin)');
        $this->line('Donor: donor@test.com / password (CANNOT access /admin)');
        $this->line('');
        $this->info('Test the access control by:');
        $this->line('1. Start the server: php artisan serve');
        $this->line('2. Visit http://localhost:8000/admin');
        $this->line('3. Try logging in with each user');
        $this->line('4. Donors should be redirected with error message');
        
        return Command::SUCCESS;
    }
}
