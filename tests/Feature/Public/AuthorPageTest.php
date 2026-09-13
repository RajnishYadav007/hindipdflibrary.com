<?php

namespace Tests\Feature\Public;

use App\Enums\BookStatus;
use App\Models\Author;
use App\Models\AuthorTranslation;
use App\Models\Book;
use App\Models\BookTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_author_page_loads_with_details_and_published_books(): void
    {
        $author = Author::create([
            'slug' => 'munshi-premchand',
            'is_active' => true,
        ]);

        AuthorTranslation::create([
            'author_id' => $author->id,
            'locale' => 'hi',
            'name' => 'मुंशी प्रेमचंद',
            'biography' => 'प्रेमचंद आधुनिक हिन्दी साहित्य के महानतम रचनाकारों में से एक हैं।',
        ]);

        $publishedBook = Book::create([
            'slug' => 'godan-premchand',
            'status' => BookStatus::Published,
            'publication_year' => 1936,
        ]);
        BookTranslation::create([
            'book_id' => $publishedBook->id,
            'locale' => 'hi',
            'title' => 'गोदान',
        ]);
        $author->books()->attach($publishedBook->id);

        $response = $this->get('/authors/munshi-premchand');

        $response->assertSuccessful();
        $response->assertSee('मुंशी प्रेमचंद');
        $response->assertSee('प्रेमचंद आधुनिक हिन्दी साहित्य');
        $response->assertSee('गोदान');
        $response->assertSee('1 Published Book');
    }

    public function test_inactive_author_returns_404(): void
    {
        $author = Author::create([
            'slug' => 'inactive-author-profile',
            'is_active' => false,
        ]);

        AuthorTranslation::create([
            'author_id' => $author->id,
            'locale' => 'hi',
            'name' => 'अक्रिय लेखक',
        ]);

        $this->get('/authors/inactive-author-profile')->assertNotFound();
    }

    public function test_author_page_only_shows_published_books(): void
    {
        $author = Author::create([
            'slug' => 'author-filtering-test',
            'is_active' => true,
        ]);
        AuthorTranslation::create([
            'author_id' => $author->id,
            'locale' => 'hi',
            'name' => 'लेखक फिल्टर टेस्ट',
        ]);

        $pub = Book::create([
            'slug' => 'author-published-book',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create([
            'book_id' => $pub->id,
            'locale' => 'hi',
            'title' => 'लेखक की प्रकाशित पुस्तक',
        ]);
        $author->books()->attach($pub->id);

        $draft = Book::create([
            'slug' => 'author-draft-book',
            'status' => BookStatus::Draft,
        ]);
        BookTranslation::create([
            'book_id' => $draft->id,
            'locale' => 'hi',
            'title' => 'लेखक की ड्राफ्ट पुस्तक',
        ]);
        $author->books()->attach($draft->id);

        $response = $this->get('/authors/author-filtering-test');

        $response->assertSuccessful();
        $response->assertSee('लेखक की प्रकाशित पुस्तक');
        $response->assertDontSee('लेखक की ड्राफ्ट पुस्तक');
    }

    public function test_singular_alias_route_redirects_301_to_authors_show(): void
    {
        $response = $this->get('/author/premchand');

        $response->assertRedirect('/authors/premchand');
        $response->assertStatus(301);
    }
}
