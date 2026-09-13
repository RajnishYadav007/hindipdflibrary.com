<?php

namespace Tests\Feature\Database;

use App\Enums\AuthorRole;
use App\Enums\ReviewStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that all Phase 3 core tables exist.
     */
    public function test_all_core_tables_exist(): void
    {
        $expectedTables = [
            'users',
            'languages',
            'authors',
            'author_translations',
            'categories',
            'category_translations',
            'genres',
            'genre_translations',
            'books',
            'book_translations',
            'book_authors',
            'book_categories',
            'book_genres',
            'book_languages',
            'reviews',
            'favorites',
            'media',
            'seo_meta',
            'redirects',
        ];

        foreach ($expectedTables as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Failed asserting that table [{$table}] exists in database."
            );
        }
    }

    /**
     * Test unique constraint: duplicate translation locale for the same book is rejected.
     */
    public function test_duplicate_book_translation_locale_is_rejected(): void
    {
        $book = Book::factory()->create();

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'गोदान',
        ]);

        $this->expectException(QueryException::class);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'गोदान द्वितीय',
        ]);
    }

    /**
     * Test unique constraint: duplicate user favorite for the same book is rejected.
     */
    public function test_duplicate_favorite_is_rejected(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Favorite::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->expectException(QueryException::class);

        Favorite::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    /**
     * Test unique constraint: duplicate review by the same user for the same book is rejected.
     */
    public function test_duplicate_user_review_for_book_is_rejected(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'title' => 'Great Book',
            'body' => 'Enjoyed reading this.',
            'status' => ReviewStatus::Approved,
        ]);

        $this->expectException(QueryException::class);

        Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 4,
            'title' => 'Second Review Attempt',
            'body' => 'Should fail due to unique constraint.',
            'status' => ReviewStatus::Approved,
        ]);
    }

    /**
     * Test unique constraint: duplicate book-author relationship with same role is rejected.
     */
    public function test_duplicate_book_author_pivot_is_rejected(): void
    {
        $book = Book::factory()->create();
        $author = Author::factory()->create();

        DB::table('book_authors')->insert([
            'book_id' => $book->id,
            'author_id' => $author->id,
            'role' => AuthorRole::Author->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('book_authors')->insert([
            'book_id' => $book->id,
            'author_id' => $author->id,
            'role' => AuthorRole::Author->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Test unique constraint: duplicate book-category pivot is rejected.
     */
    public function test_duplicate_book_category_pivot_is_rejected(): void
    {
        $book = Book::factory()->create();
        $category = Category::factory()->create();

        DB::table('book_categories')->insert([
            'book_id' => $book->id,
            'category_id' => $category->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('book_categories')->insert([
            'book_id' => $book->id,
            'category_id' => $category->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Test unique constraint: duplicate book-genre pivot is rejected.
     */
    public function test_duplicate_book_genre_pivot_is_rejected(): void
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();

        DB::table('book_genres')->insert([
            'book_id' => $book->id,
            'genre_id' => $genre->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('book_genres')->insert([
            'book_id' => $book->id,
            'genre_id' => $genre->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Test unique constraint: duplicate book-language pivot is rejected.
     */
    public function test_duplicate_book_language_pivot_is_rejected(): void
    {
        $book = Book::factory()->create();
        $language = Language::factory()->create();

        DB::table('book_languages')->insert([
            'book_id' => $book->id,
            'language_id' => $language->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        DB::table('book_languages')->insert([
            'book_id' => $book->id,
            'language_id' => $language->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Test foreign key enforcement: inserting an invalid book_id into book_translations fails.
     */
    public function test_invalid_foreign_key_is_rejected(): void
    {
        $this->expectException(QueryException::class);

        BookTranslation::create([
            'book_id' => 9999999,
            'locale' => 'en',
            'title' => 'Nonexistent Book Translation',
        ]);
    }

    /**
     * Test hierarchical self-referencing relationship on categories.
     */
    public function test_category_parent_child_hierarchy(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertTrue($parent->children->contains($child));

        // When parent is deleted, child parent_id becomes null (nullOnDelete)
        $parent->forceDelete();
        $child->refresh();
        $this->assertNull($child->parent_id);
    }
}
