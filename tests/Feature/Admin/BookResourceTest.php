<?php

namespace Tests\Feature\Admin;

use App\Models\Book;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BookResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_admin_can_render_books_index_and_see_records(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole('Super Admin');

        $book = Book::factory()->create([
            'slug' => 'godan-munshi-premchand',
        ]);

        $response = $this->actingAs($admin)->get('/admin/books');

        $response->assertSuccessful();
        $response->assertSee($book->slug);
    }

    public function test_admin_can_render_create_book_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole('Super Admin');

        $response = $this->actingAs($admin)->get('/admin/books/create');

        $response->assertSuccessful();
        $response->assertSee('Basic Information');
    }

    public function test_admin_can_render_edit_book_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole('Super Admin');

        $book = Book::factory()->create([
            'slug' => 'gaban-premchand',
        ]);

        $response = $this->actingAs($admin)->get("/admin/books/{$book->getKey()}/edit");

        $response->assertSuccessful();
        $response->assertSee('gaban-premchand');
    }
}
