<?php

namespace Tests\Feature\Public;

use App\Enums\BookStatus;
use App\Enums\ReviewStatus;
use App\Models\Author;
use App\Models\AuthorTranslation;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Media;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_book_page_loads_with_details_and_metadata(): void
    {
        $book = Book::create([
            'slug' => 'godan-test-slug',
            'status' => BookStatus::Published,
            'publication_year' => 1936,
            'publisher' => 'Saraswati Press',
            'isbn' => '978-81-7178-000-0',
            'pages' => 340,
            'rating_average' => 4.85,
            'rating_count' => 120,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'गोदान',
            'short_description' => 'भारतीय ग्रामीण जीवन का महाकाव्य',
            'description' => 'गोदान मुंशी प्रेमचंद का अंतिम और सबसे प्रसिद्ध उपन्यास है।',
            'summary' => 'होरी और धनिया की मार्मिक गाथा।',
            'why_read' => 'यह उपन्यास भारतीय समाज के वास्तविक यथार्थ को दर्शाता है।',
        ]);

        $author = Author::create([
            'slug' => 'premchand-author-test',
            'is_active' => true,
        ]);

        AuthorTranslation::create([
            'author_id' => $author->id,
            'locale' => 'hi',
            'name' => 'मुंशी प्रेमचंद',
        ]);

        $book->authors()->attach($author->id);

        $response = $this->get('/books/godan-test-slug');

        $response->assertSuccessful();
        $response->assertSee('गोदान');
        $response->assertSee('मुंशी प्रेमचंद');
        $response->assertSee('Saraswati Press');
        $response->assertSee('1936');
        $response->assertSee('340');
        $response->assertSee('भारतीय ग्रामीण जीवन का महाकाव्य');
        $response->assertSee('गोदान मुंशी प्रेमचंद का अंतिम');
        $response->assertSee('होरी और धनिया की मार्मिक गाथा');
        $response->assertSee('यह उपन्यास भारतीय समाज');
    }

    public function test_draft_or_archived_book_returns_404(): void
    {
        $draft = Book::create([
            'slug' => 'draft-book',
            'status' => BookStatus::Draft,
        ]);
        BookTranslation::create([
            'book_id' => $draft->id,
            'locale' => 'hi',
            'title' => 'ड्राफ्ट बुक',
        ]);

        $archived = Book::create([
            'slug' => 'archived-book',
            'status' => BookStatus::Archived,
        ]);
        BookTranslation::create([
            'book_id' => $archived->id,
            'locale' => 'hi',
            'title' => 'संग्रहीत बुक',
        ]);

        $this->get('/books/draft-book')->assertNotFound();
        $this->get('/books/archived-book')->assertNotFound();
    }

    public function test_book_page_displays_only_approved_reviews(): void
    {
        $book = Book::create([
            'slug' => 'book-reviews-test',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'समीक्षा परीक्षण पुस्तक',
        ]);

        $user1 = User::factory()->create(['name' => 'Approved Reviewer']);
        $user2 = User::factory()->create(['name' => 'Pending Reviewer']);

        Review::create([
            'book_id' => $book->id,
            'user_id' => $user1->id,
            'rating' => 5,
            'title' => 'उत्कृष्ट कृति',
            'body' => 'यह बहुत ही सुंदर पुस्तक है।',
            'status' => ReviewStatus::Approved,
        ]);

        Review::create([
            'book_id' => $book->id,
            'user_id' => $user2->id,
            'rating' => 1,
            'title' => 'अस्वीकृत समीक्षा',
            'body' => 'यह समीक्षा अभी पेंडिंग है।',
            'status' => ReviewStatus::Pending,
        ]);

        $response = $this->get('/books/book-reviews-test');

        $response->assertSuccessful();
        $response->assertSee('Approved Reviewer');
        $response->assertSee('उत्कृष्ट कृति');
        $response->assertSee('यह बहुत ही सुंदर पुस्तक है।');
        $response->assertDontSee('Pending Reviewer');
        $response->assertDontSee('अस्वीकृत समीक्षा');
    }

    public function test_book_page_increments_view_count_quietly(): void
    {
        $book = Book::create([
            'slug' => 'view-counter-book',
            'status' => BookStatus::Published,
            'view_count' => 10,
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'व्यू काउंटर पुस्तक',
        ]);

        $this->get('/books/view-counter-book')->assertSuccessful();

        $this->assertEquals(11, $book->fresh()->view_count);
    }

    public function test_book_page_shows_truthful_pdf_cta_when_media_present(): void
    {
        $book = Book::create([
            'slug' => 'pdf-cta-book',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'पीडीएफ पुस्तक',
        ]);

        Media::create([
            'model_type' => Book::class,
            'model_id' => $book->id,
            'uuid' => 'test-pdf-uuid-1',
            'collection_name' => 'pdf',
            'name' => 'sample_pdf',
            'file_name' => 'sample.pdf',
            'mime_type' => 'application/pdf',
            'disk' => 'public',
            'size' => 1048576,
        ]);

        $response = $this->get('/books/pdf-cta-book');

        $response->assertSuccessful();
        $response->assertSee('Read / Download PDF');
        $response->assertSee('sample.pdf');
    }

    public function test_book_page_shows_source_url_cta_when_present(): void
    {
        $book = Book::create([
            'slug' => 'source-cta-book',
            'status' => BookStatus::Published,
            'source_url' => 'https://archive.org/details/test-book',
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'सोर्स यूआरएल पुस्तक',
        ]);

        $response = $this->get('/books/source-cta-book');

        $response->assertSuccessful();
        $response->assertSee('Access at Primary Source');
        $response->assertSee('https://archive.org/details/test-book');
    }

    public function test_book_page_shows_reference_badge_when_no_media_or_source(): void
    {
        $book = Book::create([
            'slug' => 'ref-only-book',
            'status' => BookStatus::Published,
        ]);
        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'संदर्भ पुस्तक',
        ]);

        $response = $this->get('/books/ref-only-book');

        $response->assertSuccessful();
        $response->assertSee('Reference Catalog Edition');
        $response->assertDontSee('Read / Download PDF');
        $response->assertDontSee('Access at Primary Source');
    }

    public function test_singular_alias_route_redirects_301_to_books_show(): void
    {
        $response = $this->get('/book/test-alias');

        $response->assertRedirect('/books/test-alias');
        $response->assertStatus(301);
    }
}
