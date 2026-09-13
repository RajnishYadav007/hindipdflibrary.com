<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin(): void
    {
        $response = $this->get('/admin');

        // Filament redirects unauthenticated users to the admin login page
        $response->assertRedirect('/admin/login');
    }

    public function test_non_admin_user_cannot_access_admin(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        // canAccessPanel returns false -> 403 Forbidden
        $response->assertForbidden();
    }

    public function test_admin_user_can_access_admin(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertSuccessful();
    }

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('Hindi PDF Library');
    }
}
