<?php

namespace Tests\Feature\Analytics;

use App\Actions\Books\RecalculateBookMetricsAction;
use App\Actions\Reviews\SubmitReviewAction;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\User;
use App\Services\Analytics\AnalyticsService;
use App\Support\Analytics\AnalyticsEvent as EventCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsEventCaptureTest extends TestCase
{
    use RefreshDatabase;

    public function test_allowed_events_are_recorded(): void
    {
        $service = app(AnalyticsService::class);

        $event = $service->record(EventCatalog::PAGE_VIEW, [
            'visitor_id' => 'test-visitor-1234567890123456',
            'session_id' => 'test-session-1234567890123456',
            'url_path' => '/en',
        ]);

        $this->assertNotNull($event);
        $this->assertDatabaseHas('analytics_events', [
            'id' => $event->id,
            'event_name' => EventCatalog::PAGE_VIEW,
            'visitor_id' => 'test-visitor-1234567890123456',
        ]);
    }

    public function test_invalid_event_names_are_rejected(): void
    {
        $service = app(AnalyticsService::class);

        $event = $service->record('malicious_arbitrary_event', [
            'url_path' => '/fake',
        ]);

        $this->assertNull($event);
        $this->assertDatabaseMissing('analytics_events', [
            'event_name' => 'malicious_arbitrary_event',
        ]);
    }

    public function test_book_events_normalize_invalid_books(): void
    {
        $service = app(AnalyticsService::class);

        // Non-existent book_id
        $event = $service->record(EventCatalog::BOOK_VIEW, [
            'book_id' => 999999,
        ]);

        $this->assertNotNull($event);
        $this->assertNull($event->book_id);
    }

    public function test_public_book_page_records_book_view(): void
    {
        $book = Book::factory()->published()->create([
            'slug' => 'test-analytics-book',
        ]);

        $response = $this->get("/en/book/{$book->slug}");

        $response->assertStatus(200);

        $this->assertDatabaseHas('analytics_events', [
            'event_name' => EventCatalog::BOOK_VIEW,
            'book_id' => $book->id,
        ]);
    }

    public function test_draft_book_returns_404_and_does_not_record_book_view(): void
    {
        $book = Book::factory()->draft()->create([
            'slug' => 'test-draft-book',
        ]);

        $response = $this->get("/en/book/{$book->slug}");

        $response->assertStatus(404);

        $this->assertDatabaseMissing('analytics_events', [
            'event_name' => EventCatalog::BOOK_VIEW,
            'book_id' => $book->id,
        ]);
    }

    public function test_search_query_records_search_event(): void
    {
        $book = Book::factory()->published()->create([
            'slug' => 'ramcharitmanas-pdf',
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'en',
            'title' => 'Ramcharitmanas PDF',
        ]);

        $response = $this->get('/en/search?q=Ramcharitmanas');

        $response->assertStatus(200);

        $this->assertDatabaseHas('analytics_events', [
            'event_name' => EventCatalog::SEARCH,
            'search_query' => 'Ramcharitmanas',
        ]);
    }

    public function test_zero_result_searches_are_identifiable(): void
    {
        $response = $this->get('/en/search?q=NonExistentQueryXYZ123');

        $response->assertStatus(200);

        $this->assertDatabaseHas('analytics_events', [
            'event_name' => EventCatalog::SEARCH,
            'search_query' => 'NonExistentQueryXYZ123',
            'search_result_count' => 0,
        ]);
    }

    public function test_search_result_click_records_and_redirects(): void
    {
        $book = Book::factory()->published()->create([
            'slug' => 'clicked-book-slug',
        ]);

        $response = $this->get("/search/click?book_id={$book->id}&q=Gita");

        $response->assertRedirect(route('books.show', ['slug' => $book->slug]));

        $this->assertDatabaseHas('analytics_events', [
            'event_name' => EventCatalog::SEARCH_RESULT_CLICK,
            'book_id' => $book->id,
            'search_query' => 'Gita',
        ]);
    }

    public function test_rating_and_review_submissions_record_events(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->published()->create();

        $action = new SubmitReviewAction(new RecalculateBookMetricsAction);
        $action->handle(
            user: $user,
            book: $book,
            rating: 5,
            title: 'Outstanding literary work',
            body: 'Detailed review of this masterpiece.'
        );

        $this->assertDatabaseHas('analytics_events', [
            'event_name' => EventCatalog::RATING_SUBMITTED,
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('analytics_events', [
            'event_name' => EventCatalog::REVIEW_SUBMITTED,
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_bot_traffic_is_not_recorded(): void
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        ])->get('/en');

        $response->assertStatus(200);

        $this->assertDatabaseMissing('analytics_events', [
            'event_name' => EventCatalog::PAGE_VIEW,
            'url_path' => '/en',
        ]);
    }

    public function test_analytics_does_not_store_passwords_or_raw_ips(): void
    {
        $service = app(AnalyticsService::class);

        $event = $service->record(EventCatalog::PAGE_VIEW, [
            'password' => 'secret123',
            'token' => 'oauth-bearer-token',
            'ip' => '192.168.1.1',
        ]);

        $this->assertNotNull($event);
        // Table schema has no password or ip column
        $this->assertFalse(array_key_exists('password', $event->getAttributes()));
        $this->assertFalse(array_key_exists('token', $event->getAttributes()));
        $this->assertFalse(array_key_exists('ip', $event->getAttributes()));
    }
}
