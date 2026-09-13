<?php

namespace Tests\Feature\Engagement;

use App\Actions\Reviews\SubmitReviewAction;
use App\Enums\BookStatus;
use App\Enums\ReviewStatus;
use App\Livewire\BookReviewForm;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Livewire\Livewire;
use Tests\TestCase;

class RatingSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function createPublishedBook(): Book
    {
        $book = Book::create([
            'slug' => 'test-book-ratings',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'परीक्षण पुस्तक',
        ]);

        return $book;
    }

    public function test_guest_cannot_submit_rating_via_livewire(): void
    {
        $book = $this->createPublishedBook();

        Livewire::test(BookReviewForm::class, ['book' => $book])
            ->set('rating', 5)
            ->set('title', 'Great Book')
            ->set('body', 'Loved this classic.')
            ->call('submit')
            ->assertRedirect(route('login'));

        $this->assertDatabaseEmpty('reviews');
    }

    public function test_authenticated_user_can_submit_rating_and_review(): void
    {
        $book = $this->createPublishedBook();
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(BookReviewForm::class, ['book' => $book])
            ->set('rating', 4)
            ->set('title', 'Thoughtful reflection')
            ->set('body', 'Very deep character development.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true)
            ->assertSee('Thank you! Your rating and review have been submitted');

        $this->assertDatabaseHas('reviews', [
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 4,
            'title' => 'Thoughtful reflection',
            'status' => ReviewStatus::Pending->value,
            'is_verified' => true,
        ]);
    }

    public function test_rating_outside_1_to_5_is_rejected(): void
    {
        $book = $this->createPublishedBook();
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(BookReviewForm::class, ['book' => $book])
            ->set('rating', 0)
            ->call('submit')
            ->assertHasErrors(['rating']);

        Livewire::actingAs($user)
            ->test(BookReviewForm::class, ['book' => $book])
            ->set('rating', 6)
            ->call('submit')
            ->assertHasErrors(['rating']);

        $this->assertDatabaseEmpty('reviews');
    }

    public function test_user_updating_review_updates_existing_record_without_duplicate(): void
    {
        $book = $this->createPublishedBook();
        $user = User::factory()->create();

        $action = app(SubmitReviewAction::class);

        // First submission
        $review1 = $action->handle($user, $book, 3, 'Initial Thoughts', 'It was okay.');
        $this->assertEquals(1, Review::where('user_id', $user->id)->where('book_id', $book->id)->count());

        // Update submission
        $review2 = $action->handle($user, $book, 5, 'Revised View', 'Actually, it is a masterpiece.');
        $this->assertEquals($review1->id, $review2->id);
        $this->assertEquals(1, Review::where('user_id', $user->id)->where('book_id', $book->id)->count());

        $this->assertDatabaseHas('reviews', [
            'id' => $review1->id,
            'rating' => 5,
            'title' => 'Revised View',
            'body' => 'Actually, it is a masterpiece.',
            'status' => ReviewStatus::Pending->value,
        ]);
    }

    public function test_database_enforces_uniqueness_on_user_and_book(): void
    {
        $book = $this->createPublishedBook();
        $user = User::factory()->create();

        Review::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 4,
            'status' => ReviewStatus::Pending,
        ]);

        $this->expectException(QueryException::class);

        // Raw duplicate insert bypassing Eloquent action
        Review::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 5,
            'status' => ReviewStatus::Pending,
        ]);
    }

    public function test_cannot_submit_review_for_draft_or_archived_books(): void
    {
        $draft = Book::create(['slug' => 'draft-book', 'status' => BookStatus::Draft]);
        $user = User::factory()->create();

        $action = app(SubmitReviewAction::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reviews can only be submitted for published books.');

        $action->handle($user, $draft, 5);
    }
}
