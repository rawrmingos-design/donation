<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestRoleAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:test-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test role-based access control by creating test users for different roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating test users for role-based access control...');

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
        $this->info('Role-Based Access Control Test Matrix:');
        $this->line('');
        
        $this->table(
            ['Role', 'Email', 'Password', '/admin', '/dashboard', '/campaign/create', '/fundraiser/application'],
            [
                ['admin', 'admin@test.com', 'password', '✅ YES', '❌ NO (→/admin)', '❌ NO (→/admin)', '❌ NO (→/admin)'],
                ['creator', 'creator@test.com', 'password', '✅ YES', '✅ YES', '✅ YES', '❌ NO (→/dashboard)'],
                ['donor', 'donor@test.com', 'password', '❌ NO (→/login)', '✅ YES', '❌ NO (→/)', '✅ YES'],
            ]
        );
        
        $this->line('');
        $this->info('Test Instructions:');
        $this->line('1. Start the server: php artisan serve');
        $this->line('2. Test each user by logging in and accessing different routes');
        $this->line('3. Verify redirections match the table above');
        $this->line('4. Check logs for unauthorized access attempts');
        
        return Command::SUCCESS;
    }
}
