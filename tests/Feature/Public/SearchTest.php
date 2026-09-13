<?php

namespace Tests\Feature\Public;

use App\Enums\BookStatus;
use App\Models\Author;
use App\Models\AuthorTranslation;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_loads_with_filters(): void
    {
        $response = $this->get('/search');

        $response->assertSuccessful();
        $response->assertSee('Library Catalog Discovery');
        $response->assertSee('Search Query');
        $response->assertSee('Category');
        $response->assertSee('Genre');
        $response->assertSee('Language');
        $response->assertSee('Sort By');
    }

    public function test_search_by_book_title_keyword(): void
    {
        $book1 = Book::create([
            'slug' => 'godan-search',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create([
            'book_id' => $book1->id,
            'locale' => 'hi',
            'title' => 'गोदान उपन्यास',
        ]);

        $book2 = Book::create([
            'slug' => 'karmabhumi-search',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create([
            'book_id' => $book2->id,
            'locale' => 'hi',
            'title' => 'कर्मभूमि',
        ]);

        $response = $this->get('/search?q=गोदान');

        $response->assertSuccessful();
        $response->assertSee('गोदान उपन्यास');
        $response->assertDontSee('कर्मभूमि');
    }

    public function test_search_by_author_name_keyword(): void
    {
        $author = Author::create([
            'slug' => 'dinkar',
            'is_active' => true,
        ]);
        AuthorTranslation::create([
            'author_id' => $author->id,
            'locale' => 'hi',
            'name' => 'रामधारी सिंह दिनकर',
        ]);

        $book = Book::create([
            'slug' => 'rashmirathi-search',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'रश्मिरथी',
        ]);
        $book->authors()->attach($author->id);

        $otherBook = Book::create([
            'slug' => 'other-unrelated-book',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create([
            'book_id' => $otherBook->id,
            'locale' => 'hi',
            'title' => 'अन्य भिन्न पुस्तक',
        ]);

        $response = $this->get('/search?q=दिनकर');

        $response->assertSuccessful();
        $response->assertSee('रश्मिरथी');
        $response->assertDontSee('अन्य भिन्न पुस्तक');
    }

    public function test_search_by_isbn(): void
    {
        $book = Book::create([
            'slug' => 'isbn-book-search',
            'status' => BookStatus::Published,
            'isbn' => '9788171780017',
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'आईएसबीएन पुस्तक',
        ]);

        $other = Book::create([
            'slug' => 'no-isbn-book',
            'status' => BookStatus::Published,
            'isbn' => '1234567890',
        ]);
        BookTranslation::create([
            'book_id' => $other->id,
            'locale' => 'hi',
            'title' => 'अन्य संख्या',
        ]);

        $response = $this->get('/search?q=9788171780017');

        $response->assertSuccessful();
        $response->assertSee('आईएसबीएन पुस्तक');
        $response->assertDontSee('अन्य संख्या');
    }

    public function test_search_filtered_by_category(): void
    {
        $cat1 = Category::create(['slug' => 'novels', 'is_active' => true]);
        CategoryTranslation::create(['category_id' => $cat1->id, 'locale' => 'hi', 'name' => 'उपन्यास']);

        $cat2 = Category::create(['slug' => 'poetry', 'is_active' => true]);
        CategoryTranslation::create(['category_id' => $cat2->id, 'locale' => 'hi', 'name' => 'कविता']);

        $book1 = Book::create(['slug' => 'novel-book', 'status' => BookStatus::Published]);
        BookTranslation::create(['book_id' => $book1->id, 'locale' => 'hi', 'title' => 'उपन्यास विधा की पुस्तक']);
        $book1->categories()->attach($cat1->id);

        $book2 = Book::create(['slug' => 'poetry-book', 'status' => BookStatus::Published]);
        BookTranslation::create(['book_id' => $book2->id, 'locale' => 'hi', 'title' => 'कविता संग्रह पुस्तक']);
        $book2->categories()->attach($cat2->id);

        $response = $this->get('/search?category='.$cat1->id);

        $response->assertSuccessful();
        $response->assertSee('उपन्यास विधा की पुस्तक');
        $response->assertDontSee('कविता संग्रह पुस्तक');
    }

    public function test_search_filtered_by_language(): void
    {
        $langHindi = Language::create(['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी', 'is_active' => true]);
        $langSanskrit = Language::create(['code' => 'sa', 'name' => 'Sanskrit', 'native_name' => 'संस्कृतम्', 'is_active' => true]);

        $book1 = Book::create([
            'slug' => 'hindi-lang-book',
            'status' => BookStatus::Published,
            'original_language_id' => $langHindi->id,
        ]);
        BookTranslation::create(['book_id' => $book1->id, 'locale' => 'hi', 'title' => 'हिन्दी मूल पुस्तक']);

        $book2 = Book::create([
            'slug' => 'sanskrit-lang-book',
            'status' => BookStatus::Published,
            'original_language_id' => $langSanskrit->id,
        ]);
        BookTranslation::create(['book_id' => $book2->id, 'locale' => 'hi', 'title' => 'संस्कृत मूल पुस्तक']);

        $response = $this->get('/search?language='.$langHindi->id);

        $response->assertSuccessful();
        $response->assertSee('हिन्दी मूल पुस्तक');
        $response->assertDontSee('संस्कृत मूल पुस्तक');
    }

    public function test_search_only_returns_published_books(): void
    {
        $published = Book::create([
            'slug' => 'search-pub-book',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create(['book_id' => $published->id, 'locale' => 'hi', 'title' => 'सर्च परिणाम प्रकाशित पुस्तक']);

        $draft = Book::create([
            'slug' => 'search-draft-book',
            'status' => BookStatus::Draft,
        ]);
        BookTranslation::create(['book_id' => $draft->id, 'locale' => 'hi', 'title' => 'सर्च परिणाम ड्राफ्ट पुस्तक']);

        $response = $this->get('/search?q=सर्च परिणाम');

        $response->assertSuccessful();
        $response->assertSee('सर्च परिणाम प्रकाशित पुस्तक');
        $response->assertDontSee('सर्च परिणाम ड्राफ्ट पुस्तक');
    }

    public function test_search_handles_no_results_gracefully(): void
    {
        $response = $this->get('/search?q=nonexistentkeywordxyz12345');

        $response->assertSuccessful();
        $response->assertSee('No books found');
        $response->assertSee('Found 0 published books');
    }
}
