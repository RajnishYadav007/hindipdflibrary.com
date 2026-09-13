<?php

namespace Tests\Feature\Engagement;

use App\Enums\BookStatus;
use App\Enums\ReviewStatus;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class FilamentReviewModerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_moderator_approving_review_updates_book_metrics(): void
    {
        $moderator = User::factory()->create(['is_admin' => false]);
        $moderator->assignRole('Moderator');

        $book = Book::create([
            'slug' => 'filament-moderation-book',
            'status' => BookStatus::Published,
            'rating_average' => 0.00,
            'rating_count' => 0,
            'review_count' => 0,
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'मॉडरेशन पुस्तक',
        ]);

        $authorUser = User::factory()->create();

        $review = Review::create([
            'book_id' => $book->id,
            'user_id' => $authorUser->id,
            'rating' => 5,
            'title' => 'Pending review',
            'body' => 'Great literary read.',
            'status' => ReviewStatus::Pending,
        ]);

        $this->assertEquals(0, $book->fresh()->rating_count);

        // Moderator approves review
        $review->update(['status' => ReviewStatus::Approved]);

        $freshBook = $book->fresh();
        $this->assertEquals(1, $freshBook->rating_count);
        $this->assertEquals(5.00, (float) $freshBook->rating_average);
        $this->assertEquals(1, $freshBook->review_count);

        // Moderator rejects review
        $review->update(['status' => ReviewStatus::Rejected]);

        $freshBook = $book->fresh();
        $this->assertEquals(0, $freshBook->rating_count);
        $this->assertEquals(0.00, (float) $freshBook->rating_average);
        $this->assertEquals(0, $freshBook->review_count);
    }
}
