<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertForbidden();
    }

    public function test_super_admin_can_access_all_admin_resources(): void
    {
        $superAdmin = User::factory()->create([
            'is_admin' => true,
        ]);
        $superAdmin->assignRole('Super Admin');

        $endpoints = [
            '/admin',
            '/admin/books',
            '/admin/authors',
            '/admin/categories',
            '/admin/genres',
            '/admin/languages',
            '/admin/media',
            '/admin/redirects',
            '/admin/reviews',
            '/admin/seo/seo-metas',
            '/admin/users',
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->actingAs($superAdmin)->get($endpoint);
            $response->assertSuccessful();
        }
    }

    public function test_editor_can_access_content_resources_but_is_forbidden_from_user_management(): void
    {
        $editor = User::factory()->create([
            'is_admin' => false,
        ]);
        $editor->assignRole('Editor');

        // Can access Books, Authors, Categories, Genres, Languages
        $this->actingAs($editor)->get('/admin')->assertSuccessful();
        $this->actingAs($editor)->get('/admin/books')->assertSuccessful();
        $this->actingAs($editor)->get('/admin/authors')->assertSuccessful();
        $this->actingAs($editor)->get('/admin/categories')->assertSuccessful();

        // Forbidden from User Management
        $this->actingAs($editor)->get('/admin/users')->assertForbidden();
    }

    public function test_moderator_can_access_reviews_but_is_forbidden_from_user_management(): void
    {
        $moderator = User::factory()->create([
            'is_admin' => false,
        ]);
        $moderator->assignRole('Moderator');

        // Can access Reviews
        $this->actingAs($moderator)->get('/admin')->assertSuccessful();
        $this->actingAs($moderator)->get('/admin/reviews')->assertSuccessful();

        // Forbidden from User Management
        $this->actingAs($moderator)->get('/admin/users')->assertForbidden();
    }
}
