<?php

namespace Tests\Feature\Public;

use App\Enums\BookStatus;
use App\Models\Author;
use App\Models\AuthorTranslation;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully_with_curated_sections(): void
    {
        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('Hindi PDF Library');
        $response->assertSee('Discover, Read &amp; Study Classic Hindi Literature', false);
        $response->assertSee('Featured Literature');
        $response->assertSee('Distinguished Authors');
        $response->assertSee('Explore By Category');
    }

    public function test_homepage_only_displays_published_books(): void
    {
        // Create published book
        $publishedBook = Book::create([
            'slug' => 'published-book-slug',
            'status' => BookStatus::Published,
            'view_count' => 50,
        ]);
        BookTranslation::create([
            'book_id' => $publishedBook->id,
            'locale' => 'hi',
            'title' => 'प्रकाशित पुस्तक',
        ]);

        // Create draft book
        $draftBook = Book::create([
            'slug' => 'draft-book-slug',
            'status' => BookStatus::Draft,
            'view_count' => 100,
        ]);
        BookTranslation::create([
            'book_id' => $draftBook->id,
            'locale' => 'hi',
            'title' => 'अप्रकाशित ड्राफ्ट पुस्तक',
        ]);

        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('प्रकाशित पुस्तक');
        $response->assertDontSee('अप्रकाशित ड्राफ्ट पुस्तक');
    }

    public function test_homepage_displays_popular_books_in_descending_views_order(): void
    {
        $bookLow = Book::create([
            'slug' => 'low-views',
            'status' => BookStatus::Published,
            'view_count' => 10,
        ]);
        BookTranslation::create([
            'book_id' => $bookLow->id,
            'locale' => 'hi',
            'title' => 'कम लोकप्रिय पुस्तक',
        ]);

        $bookHigh = Book::create([
            'slug' => 'high-views',
            'status' => BookStatus::Published,
            'view_count' => 9999,
        ]);
        BookTranslation::create([
            'book_id' => $bookHigh->id,
            'locale' => 'hi',
            'title' => 'अति लोकप्रिय पुस्तक',
        ]);

        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('अति लोकप्रिय पुस्तक');
        $response->assertSee('कम लोकप्रिय पुस्तक');
    }

    public function test_homepage_displays_active_categories_and_authors(): void
    {
        // Active category
        $category = Category::create([
            'slug' => 'active-category',
            'is_active' => true,
        ]);
        CategoryTranslation::create([
            'category_id' => $category->id,
            'locale' => 'hi',
            'name' => 'सक्रिय श्रेणी',
        ]);

        // Inactive category
        $inactiveCat = Category::create([
            'slug' => 'inactive-category',
            'is_active' => false,
        ]);
        CategoryTranslation::create([
            'category_id' => $inactiveCat->id,
            'locale' => 'hi',
            'name' => 'निष्क्रिय श्रेणी',
        ]);

        // Active author
        $author = Author::create([
            'slug' => 'active-author',
            'is_active' => true,
        ]);
        AuthorTranslation::create([
            'author_id' => $author->id,
            'locale' => 'hi',
            'name' => 'सक्रिय लेखक',
        ]);

        // Inactive author
        $inactiveAuthor = Author::create([
            'slug' => 'inactive-author',
            'is_active' => false,
        ]);
        AuthorTranslation::create([
            'author_id' => $inactiveAuthor->id,
            'locale' => 'hi',
            'name' => 'निष्क्रिय लेखक',
        ]);

        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('सक्रिय श्रेणी');
        $response->assertDontSee('निष्क्रिय श्रेणी');
        $response->assertSee('सक्रिय लेखक');
        $response->assertDontSee('निष्क्रिय लेखक');
    }

    public function test_homepage_renders_cleanly_when_database_is_empty(): void
    {
        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('Featured Literature');
        $response->assertSee('Explore By Category');
        $response->assertSee('Distinguished Authors');
    }
}
