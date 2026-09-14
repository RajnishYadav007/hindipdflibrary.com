<?php

namespace Tests\Feature\Analytics;

use App\Filament\Pages\AnalyticsDashboard;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentAnalyticsAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_guest_is_redirected_from_analytics(): void
    {
        $response = $this->get('/admin/analytics');
        $response->assertRedirect('/admin/login');
    }

    public function test_super_admin_can_access_analytics_dashboard(): void
    {
        $superAdmin = User::factory()->create([
            'is_admin' => true,
        ]);
        $superAdmin->assignRole('Super Admin');

        $this->actingAs($superAdmin);

        $this->assertTrue(AnalyticsDashboard::canAccess());

        $response = $this->get('/admin/analytics');
        $response->assertSuccessful();
    }

    public function test_admin_with_permission_can_access_analytics_dashboard(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $admin->assignRole('Admin');

        $this->actingAs($admin);

        $this->assertTrue(AnalyticsDashboard::canAccess());

        $response = $this->get('/admin/analytics');
        $response->assertSuccessful();
    }

    public function test_editor_without_analytics_permission_cannot_access(): void
    {
        $editor = User::factory()->create([
            'is_admin' => true,
        ]);
        $editor->assignRole('Editor');

        $this->actingAs($editor);

        $this->assertFalse(AnalyticsDashboard::canAccess());

        $response = $this->get('/admin/analytics');
        $response->assertForbidden();
    }

    public function test_zero_data_state_renders_cleanly(): void
    {
        $superAdmin = User::factory()->create([
            'is_admin' => true,
        ]);
        $superAdmin->assignRole('Super Admin');

        $this->actingAs($superAdmin);

        Livewire::test(AnalyticsDashboard::class)
            ->assertSuccessful();
    }
}
