<?php

namespace Tests\Feature\SEO;

use App\Enums\BookStatus;
use App\Enums\ReviewStatus;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetadataAndStructuredDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_page_renders_canonical_and_reciprocal_hreflang(): void
    {
        $book = Book::create([
            'slug' => 'seo-book-test',
            'status' => BookStatus::Published,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'en',
            'title' => 'SEO Test Book English',
            'seo_title' => 'Custom SEO Title English',
            'seo_description' => 'Custom SEO Description English',
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'एसईओ टेस्ट पुस्तक हिन्दी',
        ]);

        $responseEn = $this->get('/en/book/seo-book-test?ref=facebook&utm_source=test');
        $responseEn->assertSuccessful();

        // Canonical must be self-consistent and strip query string
        $responseEn->assertSee('<link rel="canonical" href="'.url('/en/book/seo-book-test').'">', false);

        // Hreflang alternates
        $responseEn->assertSee('<link rel="alternate" hreflang="en" href="'.url('/en/book/seo-book-test').'">', false);
        $responseEn->assertSee('<link rel="alternate" hreflang="hi" href="'.url('/hi/book/seo-book-test').'">', false);
        $responseEn->assertSee('<link rel="alternate" hreflang="x-default" href="'.url('/en/book/seo-book-test').'">', false);

        // Custom SEO title and description used
        $responseEn->assertSee('<title>Custom SEO Title English</title>', false);
        $responseEn->assertSee('<meta name="description" content="Custom SEO Description English">', false);
    }

    public function test_hreflang_omits_nonexistent_translations(): void
    {
        $book = Book::create([
            'slug' => 'only-english-slug',
            'status' => BookStatus::Published,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'en',
            'title' => 'Only English Book',
        ]);

        $response = $this->get('/en/book/only-english-slug');
        $response->assertSuccessful();

        // English and x-default should exist
        $response->assertSee('<link rel="alternate" hreflang="en" href="'.url('/en/book/only-english-slug').'">', false);
        $response->assertSee('<link rel="alternate" hreflang="x-default" href="'.url('/en/book/only-english-slug').'">', false);

        // Hindi hreflang must NOT be generated since no Hindi translation exists
        $response->assertDontSee('<link rel="alternate" hreflang="hi"', false);
    }

    public function test_book_json_ld_includes_truthful_aggregaterating_and_approved_reviews(): void
    {
        $book = Book::create([
            'slug' => 'schema-book-test',
            'status' => BookStatus::Published,
            'rating_average' => 4.5,
            'rating_count' => 2,
            'review_count' => 1,
            'isbn13' => '978-81-0000-000-0',
            'pages' => 250,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'en',
            'title' => 'Schema Book Title',
            'short_description' => 'A great book for testing schema.',
        ]);

        $user = User::factory()->create(['name' => 'Aditi Sharma']);
        $user3 = User::factory()->create(['name' => 'Rajnish Kumar']);

        // 2 Approved reviews (5 and 4 = 4.5 average)
        Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'body' => 'Wonderful reading experience!',
            'status' => ReviewStatus::Approved,
        ]);

        Review::create([
            'user_id' => $user3->id,
            'book_id' => $book->id,
            'rating' => 4,
            'body' => 'Great literary insights.',
            'status' => ReviewStatus::Approved,
        ]);

        // 1 Pending review (should be excluded from JSON-LD)
        $user2 = User::factory()->create(['name' => 'Pending Reviewer']);
        Review::create([
            'user_id' => $user2->id,
            'book_id' => $book->id,
            'rating' => 1,
            'body' => 'Pending review content.',
            'status' => ReviewStatus::Pending,
        ]);

        $response = $this->get('/en/book/schema-book-test');
        $response->assertSuccessful();

        // Check JSON-LD contains truthful AggregateRating
        $response->assertSee('"@type": "AggregateRating"', false);
        $response->assertSee('"ratingValue": "4.5"', false);
        $response->assertSee('"ratingCount": 2', false);

        // Check JSON-LD contains approved review
        $response->assertSee('Wonderful reading experience!', false);
        $response->assertSee('Aditi Sharma', false);

        // Check JSON-LD does NOT contain unapproved review
        $response->assertDontSee('Pending Reviewer', false);
        $response->assertDontSee('Pending review content.', false);
    }

    public function test_aggregaterating_omitted_when_rating_count_is_zero(): void
    {
        $book = Book::create([
            'slug' => 'zero-ratings-book',
            'status' => BookStatus::Published,
            'rating_average' => 0,
            'rating_count' => 0,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'en',
            'title' => 'Zero Rating Book',
        ]);

        $response = $this->get('/en/book/zero-ratings-book');
        $response->assertSuccessful();

        // AggregateRating should NOT appear when count is 0
        $response->assertDontSee('"@type": "AggregateRating"', false);
    }

    public function test_search_page_outputs_noindex_robots_tag(): void
    {
        $response = $this->get('/en/search?q=godaan&category=1');
        $response->assertSuccessful();

        $response->assertSee('<meta name="robots" content="noindex, follow">', false);
        $response->assertSee('<link rel="canonical" href="'.url('/en/search').'">', false);
    }
}
