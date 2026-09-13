<?php

namespace Tests\Feature\Search;

use App\Contracts\Search\SearchEngineInterface;
use App\Enums\BookStatus;
use App\Models\Author;
use App\Models\AuthorTranslation;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Genre;
use App\Models\GenreTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchEngineTest extends TestCase
{
    use RefreshDatabase;

    protected SearchEngineInterface $searchEngine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->searchEngine = app(SearchEngineInterface::class);
    }

    public function test_title_search_finds_english_and_hindi_books(): void
    {
        $book = Book::create(['slug' => 'godan-search-test', 'status' => BookStatus::Published]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'en',
            'title' => 'Godan The Novel',
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'गोदान उपन्यास',
        ]);

        $resultsEn = $this->searchEngine->search('Godan', 'en');
        $this->assertCount(1, $resultsEn);
        $this->assertEquals($book->id, $resultsEn->first()->id);

        $resultsHi = $this->searchEngine->search('गोदान', 'hi');
        $this->assertCount(1, $resultsHi);
        $this->assertEquals($book->id, $resultsHi->first()->id);
    }

    public function test_exact_title_match_ranks_higher_than_partial_match(): void
    {
        // Book 1: partial match
        $book1 = Book::create(['slug' => 'about-godan', 'status' => BookStatus::Published]);
        BookTranslation::create(['book_id' => $book1->id, 'locale' => 'en', 'title' => 'Critical Studies About Godan']);

        // Book 2: exact match
        $book2 = Book::create(['slug' => 'godan-exact', 'status' => BookStatus::Published]);
        BookTranslation::create(['book_id' => $book2->id, 'locale' => 'en', 'title' => 'Godan']);

        $results = $this->searchEngine->search('Godan', 'en');
        $this->assertCount(2, $results);
        $this->assertEquals($book2->id, $results->first()->id, 'Exact title match must rank first');
    }

    public function test_author_search_finds_books(): void
    {
        $author = Author::create(['slug' => 'premchand-search-test', 'is_active' => true]);
        AuthorTranslation::create(['author_id' => $author->id, 'locale' => 'en', 'name' => 'Munshi Premchand']);

        $book = Book::create(['slug' => 'karmabhoomi', 'status' => BookStatus::Published]);
        BookTranslation::create(['book_id' => $book->id, 'locale' => 'en', 'title' => 'Karmabhoomi']);
        $book->authors()->attach($author->id);

        $results = $this->searchEngine->search('Premchand', 'en');
        $this->assertCount(1, $results);
        $this->assertEquals($book->id, $results->first()->id);
    }

    public function test_category_and_genre_search_finds_books(): void
    {
        $category = Category::create(['slug' => 'classic-literature', 'is_active' => true]);
        CategoryTranslation::create(['category_id' => $category->id, 'locale' => 'en', 'name' => 'Classic Literature']);

        $genre = Genre::create(['slug' => 'historical-drama', 'is_active' => true]);
        GenreTranslation::create(['genre_id' => $genre->id, 'locale' => 'en', 'name' => 'Historical Drama']);

        $book = Book::create(['slug' => 'gaban', 'status' => BookStatus::Published]);
        BookTranslation::create(['book_id' => $book->id, 'locale' => 'en', 'title' => 'Gaban']);
        $book->categories()->attach($category->id);
        $book->genres()->attach($genre->id);

        $resultsCat = $this->searchEngine->search('Literature', 'en');
        $this->assertCount(1, $resultsCat);

        $resultsGenre = $this->searchEngine->search('Drama', 'en');
        $this->assertCount(1, $resultsGenre);
    }

    public function test_isbn_search_finds_book(): void
    {
        $book = Book::create([
            'slug' => 'isbn-book',
            'status' => BookStatus::Published,
            'isbn' => '81-7178-001-X',
            'isbn13' => '978-81-7178-001-3',
        ]);
        BookTranslation::create(['book_id' => $book->id, 'locale' => 'en', 'title' => 'ISBN Book Title']);

        $res10 = $this->searchEngine->search('81-7178-001-X', 'en');
        $this->assertCount(1, $res10);

        $res13 = $this->searchEngine->search('978-81-7178-001-3', 'en');
        $this->assertCount(1, $res13);
    }

    public function test_draft_and_archived_books_are_never_returned(): void
    {
        $draft = Book::create(['slug' => 'secret-draft', 'status' => BookStatus::Draft]);
        BookTranslation::create(['book_id' => $draft->id, 'locale' => 'en', 'title' => 'Secret Draft Novel']);

        $archived = Book::create(['slug' => 'old-archived', 'status' => BookStatus::Archived]);
        BookTranslation::create(['book_id' => $archived->id, 'locale' => 'en', 'title' => 'Old Archived Novel']);

        $results = $this->searchEngine->search('Novel', 'en');
        $this->assertCount(0, $results);
    }

    public function test_faceted_filters_narrow_results(): void
    {
        $cat1 = Category::create(['slug' => 'cat-1', 'is_active' => true]);
        $cat2 = Category::create(['slug' => 'cat-2', 'is_active' => true]);

        $book1 = Book::create(['slug' => 'book-cat-1', 'status' => BookStatus::Published]);
        BookTranslation::create(['book_id' => $book1->id, 'locale' => 'en', 'title' => 'Book In Cat 1']);
        $book1->categories()->attach($cat1->id);

        $book2 = Book::create(['slug' => 'book-cat-2', 'status' => BookStatus::Published]);
        BookTranslation::create(['book_id' => $book2->id, 'locale' => 'en', 'title' => 'Book In Cat 2']);
        $book2->categories()->attach($cat2->id);

        $results = $this->searchEngine->search('Book', 'en', ['category' => $cat1->id]);
        $this->assertCount(1, $results);
        $this->assertEquals($book1->id, $results->first()->id);
    }

    public function test_malicious_and_untrusted_queries_do_not_error(): void
    {
        $maliciousQueries = [
            "'; DROP TABLE books; --",
            "' OR '1'='1",
            '"><script>alert(1)</script>',
            str_repeat('a', 500),
            '%',
            '_',
            '\\',
        ];

        foreach ($maliciousQueries as $badQuery) {
            $results = $this->searchEngine->search($badQuery, 'en');
            $this->assertNotNull($results);
        }
    }
}
