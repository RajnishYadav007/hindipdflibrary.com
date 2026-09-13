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

class LocaleRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_english_and_hindi_homepages_load_with_200(): void
    {
        $responseEn = $this->get('/en');
        $responseEn->assertSuccessful();
        $this->assertEquals('en', app()->getLocale());

        $responseHi = $this->get('/hi');
        $responseHi->assertSuccessful();
        $this->assertEquals('hi', app()->getLocale());
    }

    public function test_unsupported_locale_returns_404(): void
    {
        $response = $this->get('/xx');
        $response->assertNotFound();

        $responseBook = $this->get('/fr/book/any-book');
        $responseBook->assertNotFound();
    }

    public function test_localized_book_urls_resolve_correct_locale_and_content(): void
    {
        $book = Book::create([
            'slug' => 'godaan-locale-test',
            'status' => BookStatus::Published,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'en',
            'title' => 'Godan - The Gift of a Cow',
            'short_description' => 'English description of Godan.',
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'गोदान — एक कालजयी उपन्यास',
            'short_description' => 'गोदान का हिन्दी विवरण।',
        ]);

        $resEn = $this->get('/en/book/godaan-locale-test');
        $resEn->assertSuccessful();
        $resEn->assertSee('Godan - The Gift of a Cow');

        $resHi = $this->get('/hi/book/godaan-locale-test');
        $resHi->assertSuccessful();
        $resHi->assertSee('गोदान — एक कालजयी उपन्यास');
    }

    public function test_missing_translation_falls_back_to_english(): void
    {
        $book = Book::create([
            'slug' => 'english-only-book',
            'status' => BookStatus::Published,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'en',
            'title' => 'English Only Title',
            'short_description' => 'English only content.',
        ]);

        // When requesting Hindi route for a book with only English translation
        $response = $this->get('/hi/book/english-only-book');
        $response->assertSuccessful();
        $response->assertSee('English Only Title');
    }

    public function test_localized_author_and_category_pages_resolve(): void
    {
        $author = Author::create(['slug' => 'premchand-locale-test', 'is_active' => true]);
        AuthorTranslation::create(['author_id' => $author->id, 'locale' => 'hi', 'name' => 'प्रेमचंद']);
        AuthorTranslation::create(['author_id' => $author->id, 'locale' => 'en', 'name' => 'Premchand']);

        $cat = Category::create(['slug' => 'fiction-locale-test', 'is_active' => true]);
        CategoryTranslation::create(['category_id' => $cat->id, 'locale' => 'hi', 'name' => 'उपन्यास']);
        CategoryTranslation::create(['category_id' => $cat->id, 'locale' => 'en', 'name' => 'Fiction']);

        $resAuthorEn = $this->get('/en/author/premchand-locale-test');
        $resAuthorEn->assertSuccessful();
        $resAuthorEn->assertSee('Premchand');

        $resAuthorHi = $this->get('/hi/author/premchand-locale-test');
        $resAuthorHi->assertSuccessful();
        $resAuthorHi->assertSee('प्रेमचंद');

        $resCatEn = $this->get('/en/category/fiction-locale-test');
        $resCatEn->assertSuccessful();
        $resCatEn->assertSee('Fiction');

        $resCatHi = $this->get('/hi/category/fiction-locale-test');
        $resCatHi->assertSuccessful();
        $resCatHi->assertSee('उपन्यास');
    }

    public function test_localized_search_page_loads_with_locale_preserved(): void
    {
        $resEn = $this->get('/en/search?q=test');
        $resEn->assertSuccessful();
        $this->assertEquals('en', app()->getLocale());

        $resHi = $this->get('/hi/search?q=गोदान');
        $resHi->assertSuccessful();
        $this->assertEquals('hi', app()->getLocale());
    }
}
