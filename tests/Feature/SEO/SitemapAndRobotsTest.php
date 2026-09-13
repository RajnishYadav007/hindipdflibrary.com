<?php

namespace Tests\Feature\SEO;

use App\Enums\BookStatus;
use App\Models\Author;
use App\Models\AuthorTranslation;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapAndRobotsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_valid_xml_with_published_content(): void
    {
        $publishedBook = Book::create(['slug' => 'sitemap-pub-book', 'status' => BookStatus::Published]);
        BookTranslation::create(['book_id' => $publishedBook->id, 'locale' => 'en', 'title' => 'Pub Book EN']);
        BookTranslation::create(['book_id' => $publishedBook->id, 'locale' => 'hi', 'title' => 'Pub Book HI']);

        $draftBook = Book::create(['slug' => 'sitemap-draft-book', 'status' => BookStatus::Draft]);
        BookTranslation::create(['book_id' => $draftBook->id, 'locale' => 'en', 'title' => 'Draft Book']);

        $author = Author::create(['slug' => 'sitemap-author', 'is_active' => true]);
        AuthorTranslation::create(['author_id' => $author->id, 'locale' => 'en', 'name' => 'Active Author']);

        $inactiveAuthor = Author::create(['slug' => 'sitemap-inactive-author', 'is_active' => false]);
        AuthorTranslation::create(['author_id' => $inactiveAuthor->id, 'locale' => 'en', 'name' => 'Inactive Author']);

        $category = Category::create(['slug' => 'sitemap-category', 'is_active' => true]);
        CategoryTranslation::create(['category_id' => $category->id, 'locale' => 'en', 'name' => 'Active Category']);

        $response = $this->get('/sitemap.xml');

        $response->assertSuccessful();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));

        // Included items
        $response->assertSee('/en', false);
        $response->assertSee('/hi', false);
        $response->assertSee('/en/book/sitemap-pub-book', false);
        $response->assertSee('/hi/book/sitemap-pub-book', false);
        $response->assertSee('/en/author/sitemap-author', false);
        $response->assertSee('/en/category/sitemap-category', false);

        // Excluded items
        $response->assertDontSee('/sitemap-draft-book', false);
        $response->assertDontSee('/sitemap-inactive-author', false);
        $response->assertDontSee('/admin', false);
        $response->assertDontSee('/search', false);
    }

    public function test_robots_txt_disallows_private_and_search_routes(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertSuccessful();
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));

        $response->assertSee('User-agent: *');
        $response->assertSee('Disallow: /admin/');
        $response->assertSee('Disallow: /dashboard');
        $response->assertSee('Disallow: /login');
        $response->assertSee('Disallow: /register');
        $response->assertSee('Disallow: /search*');
        $response->assertSee('Disallow: /*/search*');
        $response->assertSee('Sitemap: '.url('/sitemap.xml'));
    }
}
