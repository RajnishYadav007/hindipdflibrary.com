<?php

namespace Tests\Feature\Engagement;

use App\Enums\BookStatus;
use App\Enums\ReviewStatus;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CachedBookMetricsTest extends TestCase
{
    use RefreshDatabase;

    protected function createPublishedBook(): Book
    {
        $book = Book::create([
            'slug' => 'metrics-book',
            'status' => BookStatus::Published,
            'rating_average' => 0.00,
            'rating_count' => 0,
            'review_count' => 0,
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'मीट्रिक परीक्षण पुस्तक',
        ]);

        return $book;
    }

    public function test_new_pending_review_does_not_affect_cached_book_metrics(): void
    {
        $book = $this->createPublishedBook();
        $user = User::factory()->create();

        Review::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 5,
            'title' => 'Pending Masterpiece',
            'body' => 'Should not count yet.',
            'status' => ReviewStatus::Pending,
        ]);

        $book->refresh();
        $this->assertEquals(0, $book->rating_count);
        $this->assertEquals(0.00, (float) $book->rating_average);
        $this->assertEquals(0, $book->review_count);
    }

    public function test_approving_reviews_updates_cached_book_metrics(): void
    {
        $book = $this->createPublishedBook();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $review1 = Review::create([
            'book_id' => $book->id,
            'user_id' => $user1->id,
            'rating' => 5,
            'title' => 'First review',
            'body' => 'Detailed text review',
            'status' => ReviewStatus::Pending,
        ]);

        $review2 = Review::create([
            'book_id' => $book->id,
            'user_id' => $user2->id,
            'rating' => 4,
            'title' => null,
            'body' => null, // rating only, no text body
            'status' => ReviewStatus::Pending,
        ]);

        // Approve review 1
        $review1->update(['status' => ReviewStatus::Approved]);
        $book->refresh();

        $this->assertEquals(1, $book->rating_count);
        $this->assertEquals(5.00, (float) $book->rating_average);
        $this->assertEquals(1, $book->review_count);

        // Approve review 2 (rating only)
        $review2->update(['status' => ReviewStatus::Approved]);
        $book->refresh();

        $this->assertEquals(2, $book->rating_count);
        $this->assertEquals(4.50, (float) $book->rating_average); // (5 + 4) / 2 = 4.5
        $this->assertEquals(1, $book->review_count); // Only review1 had text body
    }

    public function test_rejecting_or_marking_spam_removes_contribution_from_metrics(): void
    {
        $book = $this->createPublishedBook();
        $user = User::factory()->create();

        $review = Review::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 5,
            'title' => 'Initially approved',
            'body' => 'Spam content later discovered',
            'status' => ReviewStatus::Approved,
        ]);

        $book->refresh();
        $this->assertEquals(1, $book->rating_count);
        $this->assertEquals(5.00, (float) $book->rating_average);

        // Reject
        $review->update(['status' => ReviewStatus::Rejected]);
        $book->refresh();
        $this->assertEquals(0, $book->rating_count);
        $this->assertEquals(0.00, (float) $book->rating_average);
        $this->assertEquals(0, $book->review_count);

        // Mark Spam
        $review->update(['status' => ReviewStatus::Spam]);
        $book->refresh();
        $this->assertEquals(0, $book->rating_count);
        $this->assertEquals(0.00, (float) $book->rating_average);
    }

    public function test_editing_approved_review_rating_recalculates_metrics(): void
    {
        $book = $this->createPublishedBook();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Review::create([
            'book_id' => $book->id,
            'user_id' => $user1->id,
            'rating' => 4,
            'status' => ReviewStatus::Approved,
        ]);

        $review2 = Review::create([
            'book_id' => $book->id,
            'user_id' => $user2->id,
            'rating' => 2,
            'status' => ReviewStatus::Approved,
        ]);

        $book->refresh();
        $this->assertEquals(3.00, (float) $book->rating_average); // (4 + 2) / 2 = 3.0

        // User 2 edits rating to 5
        $review2->update(['rating' => 5]);
        $book->refresh();
        $this->assertEquals(4.50, (float) $book->rating_average); // (4 + 5) / 2 = 4.5
    }

    public function test_deleting_approved_review_recalculates_metrics(): void
    {
        $book = $this->createPublishedBook();
        $user = User::factory()->create();

        $review = Review::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 5,
            'title' => 'To be deleted',
            'body' => 'Some text',
            'status' => ReviewStatus::Approved,
        ]);

        $book->refresh();
        $this->assertEquals(1, $book->rating_count);

        $review->delete();
        $book->refresh();
        $this->assertEquals(0, $book->rating_count);
        $this->assertEquals(0.00, (float) $book->rating_average);
        $this->assertEquals(0, $book->review_count);
    }
}
