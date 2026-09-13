<?php

namespace Tests\Feature\Database;

use App\Enums\AuthorRole;
use App\Enums\BookStatus;
use App\Enums\ReviewStatus;
use App\Models\Author;
use App\Models\AuthorTranslation;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\GenreTranslation;
use App\Models\Language;
use App\Models\Media;
use App\Models\Review;
use App\Models\SeoMeta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationshipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Book relations: Authors, Categories, Genres, Languages, Translations, Reviews, Favorites.
     */
    public function test_book_relationships_work_correctly(): void
    {
        $langHi = Language::factory()->create(['code' => 'hi', 'name' => 'Hindi']);
        $langEn = Language::factory()->create(['code' => 'en', 'name' => 'English']);

        $author = Author::factory()->create();
        AuthorTranslation::create([
            'author_id' => $author->id,
            'locale' => 'hi',
            'name' => 'मुंशी प्रेमचंद',
        ]);

        $category = Category::factory()->create();
        CategoryTranslation::create([
            'category_id' => $category->id,
            'locale' => 'hi',
            'name' => 'उपन्यास',
        ]);

        $genre = Genre::factory()->create();
        GenreTranslation::create([
            'genre_id' => $genre->id,
            'locale' => 'hi',
            'name' => 'सामाजिक यथार्थवाद',
        ]);

        $book = Book::factory()->create([
            'original_language_id' => $langHi->id,
            'status' => BookStatus::Published,
        ]);

        // Translations
        $transHi = BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'गोदान',
            'short_description' => 'कृषक जीवन की महागाथा।',
            'key_themes' => ['कृषक संघर्ष', 'होरी'],
        ]);

        $transEn = BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'en',
            'title' => 'Godaan',
            'short_description' => 'The gift of a cow.',
            'key_themes' => ['Peasantry', 'Debt'],
        ]);

        // Attach Many-to-Many
        $book->authors()->attach($author->id, ['role' => AuthorRole::Author->value]);
        $book->categories()->attach($category->id);
        $book->genres()->attach($genre->id);
        $book->languages()->attach([$langHi->id, $langEn->id]);

        // Reviews and Favorites
        $user = User::factory()->create();
        $review = Review::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 5,
            'title' => 'Masterpiece',
            'body' => 'Incredible novel.',
            'status' => ReviewStatus::Approved,
        ]);

        $favorite = Favorite::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        // Polymorphic SEO
        $seo = SeoMeta::create([
            'seoable_type' => Book::class,
            'seoable_id' => $book->id,
            'locale' => 'hi',
            'canonical_url' => 'https://hindipdflibrary.com/hi/book/' . $book->slug,
            'robots' => 'index, follow',
        ]);

        // Polymorphic Media
        $media = Media::create([
            'model_type' => Book::class,
            'model_id' => $book->id,
            'collection_name' => 'covers',
            'name' => 'godaan-cover',
            'file_name' => 'godaan-cover.webp',
            'mime_type' => 'image/webp',
            'disk' => 'public',
            'size' => 45000,
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
        ]);

        // Assertions
        $this->assertTrue($book->authors->contains($author));
        $this->assertEquals(AuthorRole::Author, $book->authors->first()->pivot->role);
        $this->assertTrue($book->categories->contains($category));
        $this->assertTrue($book->genres->contains($genre));
        $this->assertEquals(2, $book->languages->count());
        $this->assertEquals($langHi->id, $book->originalLanguage->id);
        $this->assertEquals(2, $book->translations->count());
        $this->assertTrue($book->reviews->contains($review));
        $this->assertTrue($book->favorites->contains($favorite));
        $this->assertEquals($seo->id, $book->seoMeta->id);
        $this->assertTrue($book->media->contains($media));

        // Reverse assertions
        $this->assertTrue($author->books->contains($book));
        $this->assertTrue($category->books->contains($book));
        $this->assertTrue($genre->books->contains($book));
        $this->assertTrue($langHi->books->contains($book));
        $this->assertTrue($user->reviews->contains($review));
        $this->assertTrue($user->favorites->contains($favorite));
        $this->assertTrue($user->favoriteBooks->contains($book));
    }

    /**
     * Test soft delete preservation.
     */
    public function test_soft_deletes_preserve_record_identity(): void
    {
        $book = Book::factory()->create();
        $bookId = $book->id;

        $book->delete();

        $this->assertSoftDeleted('books', ['id' => $bookId]);
        $this->assertNotNull(Book::withTrashed()->find($bookId));
    }
}
