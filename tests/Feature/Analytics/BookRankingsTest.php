<?php

namespace Tests\Feature\Analytics;

use App\Enums\BookStatus;
use App\Filament\Widgets\AnalyticsTopBooksWidget;
use App\Models\AnalyticsEvent;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Support\Analytics\AnalyticsEvent as EventCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookRankingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_most_viewed_ranking_orders_correctly(): void
    {
        $bookLow = Book::create([
            'slug' => 'low-views-book',
            'status' => BookStatus::Published,
            'view_count' => 10,
        ]);
        BookTranslation::create([
            'book_id' => $bookLow->id,
            'locale' => 'en',
            'title' => 'Low Views Book',
        ]);

        $bookHigh = Book::create([
            'slug' => 'high-views-book',
            'status' => BookStatus::Published,
            'view_count' => 500,
        ]);
        BookTranslation::create([
            'book_id' => $bookHigh->id,
            'locale' => 'en',
            'title' => 'High Views Book',
        ]);

        $widget = new AnalyticsTopBooksWidget;
        $widget->setRankingType('most_viewed');
        $books = $widget->getBooks();

        $this->assertNotEmpty($books);
        $this->assertSame('High Views Book', $books->first()['title']);
    }

    public function test_highest_rated_ranking_enforces_minimum_rating_threshold(): void
    {
        // 1 review of 5.0 (should be excluded due to < 3 threshold)
        $bookSingleFive = Book::create([
            'slug' => 'single-5-star-book',
            'status' => BookStatus::Published,
            'rating_average' => 5.00,
            'rating_count' => 1,
        ]);
        BookTranslation::create([
            'book_id' => $bookSingleFive->id,
            'locale' => 'en',
            'title' => 'Single 5 Star Book',
        ]);

        // 10 reviews of 4.60 (should be included)
        $bookPopularGood = Book::create([
            'slug' => 'verified-good-book',
            'status' => BookStatus::Published,
            'rating_average' => 4.60,
            'rating_count' => 10,
        ]);
        BookTranslation::create([
            'book_id' => $bookPopularGood->id,
            'locale' => 'en',
            'title' => 'Verified Good Book',
        ]);

        $widget = new AnalyticsTopBooksWidget;
        $widget->setRankingType('highest_rated');
        $books = $widget->getBooks();

        $this->assertCount(1, $books);
        $this->assertSame('Verified Good Book', $books->first()['title']);
    }

    public function test_trending_ranking_calculates_recent_views(): void
    {
        $bookSteady = Book::create([
            'slug' => 'steady-book',
            'status' => BookStatus::Published,
            'view_count' => 1000,
        ]);
        BookTranslation::create([
            'book_id' => $bookSteady->id,
            'locale' => 'en',
            'title' => 'Steady Book',
        ]);

        $bookSurging = Book::create([
            'slug' => 'surging-book',
            'status' => BookStatus::Published,
            'view_count' => 200,
        ]);
        BookTranslation::create([
            'book_id' => $bookSurging->id,
            'locale' => 'en',
            'title' => 'Surging Book',
        ]);

        // Seed recent views in last 2 days for surging book
        for ($i = 0; $i < 5; $i++) {
            AnalyticsEvent::create([
                'event_name' => EventCatalog::BOOK_VIEW,
                'book_id' => $bookSurging->id,
                'occurred_at' => now()->subDay(),
            ]);
        }

        $widget = new AnalyticsTopBooksWidget;
        $widget->setRankingType('trending');
        $books = $widget->getBooks();

        $this->assertNotEmpty($books);
        $this->assertSame('Surging Book', $books->first()['title']);
    }
}
