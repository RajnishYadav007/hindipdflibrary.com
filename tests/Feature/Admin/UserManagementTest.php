<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_super_admin_can_render_users_index(): void
    {
        $superAdmin = User::factory()->create(['is_admin' => true]);
        $superAdmin->assignRole('Super Admin');

        $user = User::factory()->create([
            'email' => 'author.user@example.com',
        ]);

        $response = $this->actingAs($superAdmin)->get('/admin/users');

        $response->assertSuccessful();
        $response->assertSee('author.user@example.com');
    }

    public function test_super_admin_can_render_create_user_page(): void
    {
        $superAdmin = User::factory()->create(['is_admin' => true]);
        $superAdmin->assignRole('Super Admin');

        $response = $this->actingAs($superAdmin)->get('/admin/users/create');

        $response->assertSuccessful();
        $response->assertSee('Password');
    }

    public function test_password_hash_is_not_exposed_in_edit_form(): void
    {
        $superAdmin = User::factory()->create(['is_admin' => true]);
        $superAdmin->assignRole('Super Admin');

        $targetUser = User::factory()->create([
            'email' => 'target.user@example.com',
            'password' => bcrypt('SecretPlainTextPassword123!'),
        ]);

        $response = $this->actingAs($superAdmin)->get("/admin/users/{$targetUser->getKey()}/edit");

        $response->assertSuccessful();
        $response->assertDontSee($targetUser->password);
        $response->assertDontSee('SecretPlainTextPassword123!');
    }

    public function test_editor_cannot_access_user_management(): void
    {
        $editor = User::factory()->create(['is_admin' => false]);
        $editor->assignRole('Editor');

        $response = $this->actingAs($editor)->get('/admin/users');

        $response->assertForbidden();
    }
}
