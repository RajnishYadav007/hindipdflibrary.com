<?php

namespace Tests\Feature\Admin;

use App\Enums\ReviewStatus;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ReviewModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_moderator_can_render_review_moderation_queue(): void
    {
        $moderator = User::factory()->create(['is_admin' => false]);
        $moderator->assignRole('Moderator');

        $review = Review::factory()->create([
            'status' => ReviewStatus::Pending,
            'title' => 'Pending Community Review Title',
        ]);

        $response = $this->actingAs($moderator)->get('/admin/reviews');

        $response->assertSuccessful();
        $response->assertSee('Pending Community');
    }

    public function test_moderator_can_render_edit_review_page(): void
    {
        $moderator = User::factory()->create(['is_admin' => false]);
        $moderator->assignRole('Moderator');

        $review = Review::factory()->create([
            'status' => ReviewStatus::Pending,
            'title' => 'Review For Approval',
        ]);

        $response = $this->actingAs($moderator)->get("/admin/reviews/{$review->getKey()}/edit");

        $response->assertSuccessful();
        $response->assertSee('Review For Approval');
    }

    public function test_unauthorized_user_cannot_access_review_moderation(): void
    {
        $regularUser = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($regularUser)->get('/admin/reviews');

        $response->assertForbidden();
    }
}
