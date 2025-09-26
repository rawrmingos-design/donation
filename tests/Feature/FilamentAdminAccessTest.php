<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_filament_panel()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com'
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin');
        
        $response->assertStatus(200);
    }

    public function test_creator_can_access_filament_panel()
    {
        $creator = User::factory()->create([
            'role' => 'creator',
            'email' => 'creator@test.com'
        ]);

        $this->actingAs($creator);

        $response = $this->get('/admin');
        
        $response->assertStatus(200);
    }

    public function test_donor_cannot_access_filament_panel()
    {
        $donor = User::factory()->create([
            'role' => 'donor',
            'email' => 'donor@test.com'
        ]);

        $this->actingAs($donor);

        $response = $this->get('/admin');
        
        // Should be redirected to login with error message
        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Access denied. Admin privileges required.');
    }

    public function test_unauthenticated_user_cannot_access_filament_panel()
    {
        $response = $this->get('/admin');
        
        // Should be redirected to login
        $response->assertRedirect('/login');
    }

    public function test_donor_cannot_access_any_filament_routes()
    {
        $donor = User::factory()->create([
            'role' => 'donor',
            'email' => 'donor@test.com'
        ]);

        $this->actingAs($donor);

        // Test various Filament routes
        $filamentRoutes = [
            '/admin',
            '/admin/campaigns',
            '/admin/donations',
            '/admin/users',
            '/admin/categories'
        ];

        foreach ($filamentRoutes as $route) {
            $response = $this->get($route);
            
            // All should redirect to login or return 403
            $this->assertTrue(
                $response->isRedirect() || $response->status() === 403,
                "Route {$route} should be inaccessible to donors"
            );
        }
    }

    public function test_user_model_can_access_panel_method()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $creator = User::factory()->create(['role' => 'creator']);
        $donor = User::factory()->create(['role' => 'donor']);

        // Mock panel object
        $panel = $this->createMock(\Filament\Panel::class);

        $this->assertTrue($admin->canAccessPanel($panel));
        $this->assertTrue($creator->canAccessPanel($panel));
        $this->assertFalse($donor->canAccessPanel($panel));
    }
}
