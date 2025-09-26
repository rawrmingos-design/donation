<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_access_dashboard()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com'
        ]);

        $this->actingAs($admin);

        $response = $this->get('/dashboard');
        
        // Should be redirected to admin panel
        $response->assertRedirect('/admin');
        $response->assertSessionHas('info', 'Admin users should use the admin panel.');
    }

    public function test_donor_can_access_dashboard()
    {
        $donor = User::factory()->create([
            'role' => 'donor',
            'email' => 'donor@test.com'
        ]);

        $this->actingAs($donor);

        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
    }

    public function test_creator_can_access_dashboard()
    {
        $creator = User::factory()->create([
            'role' => 'creator',
            'email' => 'creator@test.com'
        ]);

        $this->actingAs($creator);

        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_campaign_create()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com'
        ]);

        $this->actingAs($admin);

        $response = $this->get('/campaign/create');
        
        // Should be redirected to admin panel
        $response->assertRedirect('/admin');
        $response->assertSessionHas('info', 'Admin users should use the admin panel.');
    }

    public function test_creator_can_access_campaign_create()
    {
        $creator = User::factory()->create([
            'role' => 'creator',
            'email' => 'creator@test.com'
        ]);

        $this->actingAs($creator);

        $response = $this->get('/campaign/create');
        
        $response->assertStatus(200);
    }

    public function test_donor_cannot_access_campaign_create()
    {
        $donor = User::factory()->create([
            'role' => 'donor',
            'email' => 'donor@test.com'
        ]);

        $this->actingAs($donor);

        $response = $this->get('/campaign/create');
        
        // Should be redirected to home with info message
        $response->assertRedirect('/');
        $response->assertSessionHas('info', 'Access restricted. Please explore campaigns or make donations.');
    }

    public function test_admin_cannot_access_fundraiser_application()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com'
        ]);

        $this->actingAs($admin);

        $response = $this->get('/fundraiser/application');
        
        // Should be redirected to admin panel
        $response->assertRedirect('/admin');
        $response->assertSessionHas('info', 'Admin users should use the admin panel.');
    }

    public function test_donor_can_access_fundraiser_application()
    {
        $donor = User::factory()->create([
            'role' => 'donor',
            'email' => 'donor@test.com'
        ]);

        $this->actingAs($donor);

        $response = $this->get('/fundraiser/application');
        
        $response->assertStatus(200);
    }

    public function test_creator_cannot_access_fundraiser_application()
    {
        $creator = User::factory()->create([
            'role' => 'creator',
            'email' => 'creator@test.com'
        ]);

        $this->actingAs($creator);

        $response = $this->get('/fundraiser/application');
        
        // Should be redirected to dashboard with info message
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('info', 'Access restricted. Please use appropriate features for your role.');
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $protectedRoutes = [
            '/dashboard',
            '/campaign/create',
            '/fundraiser/application'
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            
            // Should be redirected to login
            $response->assertRedirect('/login');
        }
    }
}
