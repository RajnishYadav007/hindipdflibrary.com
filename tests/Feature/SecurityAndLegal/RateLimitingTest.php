<?php

namespace Tests\Feature\SecurityAndLegal;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_downloads_route_is_rate_limited(): void
    {
        Storage::fake('private_pdfs');

        $book = Book::create([
            'slug' => 'throttled-book',
            'status' => BookStatus::Published,
            'copyright_status' => CopyrightStatus::PublicDomain,
        ]);

        Storage::disk('private_pdfs')->put("books/{$book->slug}.pdf", "%PDF-1.4\nTest Content");

        $url = route('books.download', ['slug' => $book->slug]);

        // Limit is 30 per minute
        for ($i = 0; $i < 30; $i++) {
            $response = $this->get($url);
            $this->assertEquals(200, $response->getStatusCode(), "Request {$i} failed");
        }

        // 31st request should be throttled
        $throttledResponse = $this->get($url);
        $this->assertEquals(429, $throttledResponse->getStatusCode());
    }

    public function test_search_route_is_rate_limited(): void
    {
        // Clear rate limiter first
        RateLimiter::clear('search');

        // Search limit is 60 per minute
        for ($i = 0; $i < 60; $i++) {
            $response = $this->get('/search?q=test');
            $this->assertNotEquals(429, $response->getStatusCode(), "Request {$i} unexpectedly throttled");
        }

        // 61st request should be throttled
        $throttledResponse = $this->get('/search?q=test');
        $this->assertEquals(429, $throttledResponse->getStatusCode());
    }
}
