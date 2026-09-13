<?php

namespace Tests\Feature\Engagement;

use App\Enums\BookStatus;
use App\Enums\ReviewStatus;
use App\Livewire\BookReviewForm;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BookPageEngagementTest extends TestCase
{
    use RefreshDatabase;

    protected function createPublishedBook(): Book
    {
        $book = Book::create([
            'slug' => 'public-engagement-book',
            'status' => BookStatus::Published,
            'rating_average' => 4.50,
            'rating_count' => 2,
            'review_count' => 1,
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'पब्लिक एंगेजमेंट बुक',
        ]);

        return $book;
    }

    public function test_public_book_page_displays_only_approved_reviews(): void
    {
        $book = $this->createPublishedBook();
        $user1 = User::factory()->create(['name' => 'Approved Reader']);
        $user2 = User::factory()->create(['name' => 'Pending Reader']);
        $user3 = User::factory()->create(['name' => 'Spam Reader']);

        Review::create([
            'book_id' => $book->id,
            'user_id' => $user1->id,
            'rating' => 5,
            'title' => 'Approved Masterpiece',
            'body' => 'Publicly visible review text.',
            'status' => ReviewStatus::Approved,
        ]);

        Review::create([
            'book_id' => $book->id,
            'user_id' => $user2->id,
            'rating' => 4,
            'title' => 'Pending Critique',
            'body' => 'Hidden pending review text.',
            'status' => ReviewStatus::Pending,
        ]);

        Review::create([
            'book_id' => $book->id,
            'user_id' => $user3->id,
            'rating' => 1,
            'title' => 'Spam Abuse',
            'body' => 'Spam content.',
            'status' => ReviewStatus::Spam,
        ]);

        $response = $this->get('/books/public-engagement-book');

        $response->assertSuccessful();
        $response->assertSee('Approved Reader');
        $response->assertSee('Approved Masterpiece');
        $response->assertSee('Publicly visible review text.');
        $response->assertDontSee('Pending Reader');
        $response->assertDontSee('Hidden pending review text.');
        $response->assertDontSee('Spam Reader');
    }

    public function test_rating_distribution_calculates_accurately(): void
    {
        $book = Book::create([
            'slug' => 'distribution-test-book',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'रेटिंग वितरण पुस्तक',
        ]);

        $u1 = User::factory()->create();
        $u2 = User::factory()->create();
        $u3 = User::factory()->create();

        // 2 five-star reviews, 1 four-star review
        Review::create(['book_id' => $book->id, 'user_id' => $u1->id, 'rating' => 5, 'status' => ReviewStatus::Approved]);
        Review::create(['book_id' => $book->id, 'user_id' => $u2->id, 'rating' => 5, 'status' => ReviewStatus::Approved]);
        Review::create(['book_id' => $book->id, 'user_id' => $u3->id, 'rating' => 4, 'status' => ReviewStatus::Approved]);

        $response = $this->get('/books/distribution-test-book');

        $response->assertSuccessful();
        $response->assertSee('5 <span class="text-amber-400">★</span>', false);
        $response->assertSee('4 <span class="text-amber-400">★</span>', false);
    }

    public function test_guest_sees_signin_callout_in_review_form(): void
    {
        $book = $this->createPublishedBook();

        Livewire::test(BookReviewForm::class, ['book' => $book])
            ->assertSee('Rate and Review this Book')
            ->assertSee('Sign In to Rate &amp; Review', false);
    }

    public function test_authenticated_user_sees_their_pending_review_status(): void
    {
        $book = $this->createPublishedBook();
        $user = User::factory()->create();

        Review::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 5,
            'title' => 'My Pending Thoughts',
            'body' => 'I loved it.',
            'status' => ReviewStatus::Pending,
        ]);

        Livewire::actingAs($user)
            ->test(BookReviewForm::class, ['book' => $book])
            ->assertSee('Pending Moderation')
            ->assertSee('My Pending Thoughts')
            ->assertSee('Edit Review');
    }
}
