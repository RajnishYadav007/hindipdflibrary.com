<?php

namespace Tests\Feature\Search;

use App\Enums\BookStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Services\Discovery\BookDiscoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscoveryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BookDiscoveryService $discoveryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->discoveryService = app(BookDiscoveryService::class);
    }

    public function test_related_by_author_excludes_current_book_and_drafts(): void
    {
        $author = Author::create(['slug' => 'shared-author', 'is_active' => true]);

        $currentBook = Book::create(['slug' => 'current-book', 'status' => BookStatus::Published]);
        $currentBook->authors()->attach($author->id);

        $otherPublishedBook = Book::create(['slug' => 'other-published', 'status' => BookStatus::Published]);
        $otherPublishedBook->authors()->attach($author->id);

        $draftBook = Book::create(['slug' => 'draft-book', 'status' => BookStatus::Draft]);
        $draftBook->authors()->attach($author->id);

        $related = $this->discoveryService->relatedByAuthor($currentBook, 5);

        $this->assertCount(1, $related);
        $this->assertEquals($otherPublishedBook->id, $related->first()->id);
        $this->assertFalse($related->contains('id', $currentBook->id));
        $this->assertFalse($related->contains('id', $draftBook->id));
    }

    public function test_related_by_category_excludes_current_and_specified_exclusions(): void
    {
        $category = Category::create(['slug' => 'shared-category', 'is_active' => true]);

        $book1 = Book::create(['slug' => 'cat-book-1', 'status' => BookStatus::Published]);
        $book1->categories()->attach($category->id);

        $book2 = Book::create(['slug' => 'cat-book-2', 'status' => BookStatus::Published]);
        $book2->categories()->attach($category->id);

        $book3 = Book::create(['slug' => 'cat-book-3', 'status' => BookStatus::Published]);
        $book3->categories()->attach($category->id);

        // Exclude book 2
        $related = $this->discoveryService->relatedByCategory($book1, 5, [$book2->id]);

        $this->assertCount(1, $related);
        $this->assertEquals($book3->id, $related->first()->id);
    }

    public function test_popular_and_featured_books_only_return_published_books(): void
    {
        $published = Book::create([
            'slug' => 'popular-published',
            'status' => BookStatus::Published,
            'view_count' => 100,
            'rating_average' => 4.9,
            'rating_count' => 20,
        ]);

        Book::create([
            'slug' => 'popular-draft',
            'status' => BookStatus::Draft,
            'view_count' => 999,
            'rating_average' => 5.0,
            'rating_count' => 100,
        ]);

        $popular = $this->discoveryService->popularBooks(5);
        $this->assertCount(1, $popular);
        $this->assertEquals($published->id, $popular->first()->id);

        $featured = $this->discoveryService->featuredBooks(5);
        $this->assertCount(1, $featured);
        $this->assertEquals($published->id, $featured->first()->id);
    }
}
