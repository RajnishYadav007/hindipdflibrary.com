<?php

namespace Tests\Feature\Public;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Models\Author;
use App\Models\AuthorTranslation;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Services\Archive\ArchiveOrgService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractiveReaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_archive_service_extracts_identifier_correctly(): void
    {
        $service = new ArchiveOrgService;

        $this->assertEquals('godan00prem', $service->extractIdentifier('https://archive.org/details/godan00prem'));
        $this->assertEquals('godan00prem', $service->extractIdentifier('https://archive.org/stream/godan00prem'));
        $this->assertEquals('godan00prem', $service->extractIdentifier('https://archive.org/download/godan00prem/godan.pdf'));
        $this->assertEquals('Godan-Hindi', $service->extractIdentifier('https://archive.org/download/Godan-Hindi/GodaanByMunshiPremchandEbook.pdf'));
        $this->assertEquals('in.ernet.dli.2015.405490', $service->extractIdentifier('https://archive.org/details/in.ernet.dli.2015.405490'));
        $this->assertEquals('simple_id_123', $service->extractIdentifier('simple_id_123'));
        $this->assertNull($service->extractIdentifier(null));
        $this->assertNull($service->extractIdentifier(''));
        $this->assertNull($service->extractIdentifier('https://example.com/books/sample'));
    }

    public function test_archive_service_embed_url_generation(): void
    {
        $service = new ArchiveOrgService;

        $embedUrl = $service->getEmbedUrl('godan00prem');
        $this->assertStringContainsString('archive.org/embed/godan00prem', $embedUrl);

        $detailsUrl = $service->getDetailsUrl('godan00prem');
        $this->assertStringContainsString('archive.org/details/godan00prem', $detailsUrl);
    }

    public function test_archive_service_manifest_structure_has_required_keys(): void
    {
        $service = new ArchiveOrgService;

        // Use a mock-style test — the manifest should always return the required structure
        // even if Archive.org is unreachable (fallback embed mode)
        // We test with a nonexistent identifier to trigger fallback
        $manifest = $service->getManifest('__nonexistent_test_identifier_xyz__');

        $this->assertNotNull($manifest);
        $this->assertArrayHasKey('identifier', $manifest);
        $this->assertArrayHasKey('page_count', $manifest);
        $this->assertArrayHasKey('pages', $manifest);
        $this->assertArrayHasKey('reader_mode', $manifest);
        $this->assertArrayHasKey('embed_url', $manifest);
        $this->assertArrayHasKey('details_url', $manifest);
        $this->assertIsArray($manifest['pages']);

        // reader_mode must be a valid value
        $this->assertContains($manifest['reader_mode'], ['scans', 'embed', 'text']);
    }

    public function test_interactive_reader_page_loads_successfully_for_published_book(): void
    {
        $book = Book::create([
            'slug' => 'kamayani-classic',
            'status' => BookStatus::Published,
            'source_url' => 'https://archive.org/details/kamayani_test',
            'publication_year' => 1936,
            'pages' => 280,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'कामायनी',
            'description' => 'जयशंकर प्रसाद द्वारा रचित हिन्दी का कालजयी महाकाव्य।',
        ]);

        $author = Author::create(['slug' => 'prasad', 'is_active' => true]);
        AuthorTranslation::create(['author_id' => $author->id, 'locale' => 'hi', 'name' => 'जयशंकर प्रसाद']);
        $book->authors()->attach($author->id);

        $response = $this->get("/book/{$book->slug}/reader");

        $response->assertSuccessful();
        $response->assertSee('कामायनी');
        $response->assertSee('जयशंकर प्रसाद');
        $response->assertSee('readerApp');
        $response->assertSee('speechSynthesis');
    }

    public function test_interactive_reader_returns_404_for_draft_book(): void
    {
        $book = Book::create([
            'slug' => 'draft-interactive-book',
            'status' => BookStatus::Draft,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'अप्रकाशित पुस्तक',
        ]);

        $response = $this->get("/book/{$book->slug}/reader");
        $response->assertNotFound();
    }

    public function test_archive_manifest_json_endpoint_returns_valid_structure(): void
    {
        $book = Book::create([
            'slug' => 'godan-manifest-test',
            'status' => BookStatus::Published,
            'source_url' => 'https://archive.org/details/godan_archive_id',
            'pages' => 312,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'गोदान',
            'description' => 'मुंशी प्रेमचंद का अमर उपन्यास।',
        ]);

        $response = $this->get("/book/{$book->slug}/manifest");

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'identifier',
            'title',
            'reader_mode',
            'embed_url',
            'page_count',
            'pages',
            'has_ocr',
        ]);
        // reader_mode must be a known value
        $this->assertContains($response->json('reader_mode'), ['scans', 'embed', 'text']);
    }

    public function test_archive_manifest_returns_embed_url_for_archive_book(): void
    {
        $book = Book::create([
            'slug' => 'embed-manifest-test',
            'status' => BookStatus::Published,
            'source_url' => 'https://archive.org/details/some_book_id',
            'pages' => 100,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'परीक्षण पुस्तक',
        ]);

        $response = $this->get("/book/{$book->slug}/manifest");

        $response->assertSuccessful();
        // For an Archive.org book, embed_url should point to archive.org
        $this->assertStringContainsString('archive.org', $response->json('embed_url'));
    }

    public function test_book_show_page_includes_interactive_reader_cta(): void
    {
        $book = Book::create([
            'slug' => 'rashmirathi-reader-cta',
            'status' => BookStatus::Published,
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'रश्मिरथी',
        ]);

        $response = $this->get(route('books.show', ['slug' => $book->slug]));

        $response->assertSuccessful();
        $response->assertSee('Read Online (ऑनलाइन पढ़ें)');
        $response->assertDontSee('Interactive Audio');
        $response->assertSee(route('books.interactive_reader', ['slug' => $book->slug]));
    }

    public function test_read_action_redirects_to_interactive_reader_without_enum_error(): void
    {
        $book = Book::create([
            'slug' => 'godaan-read-action-test',
            'status' => BookStatus::Published,
            'copyright_status' => CopyrightStatus::PublicDomain,
            'source_url' => 'https://archive.org/download/Godan-Hindi/GodaanByMunshiPremchandEbook.pdf',
        ]);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'hi',
            'title' => 'गोदान',
        ]);

        // This endpoint was previously throwing Object of class App\Enums\CopyrightStatus could not be converted to string
        $response = $this->get("/book/{$book->slug}/read");

        $response->assertRedirect(route('books.interactive_reader', ['slug' => $book->slug]));
    }

    public function test_book_with_copyright_status_enum_does_not_throw_in_has_external_source_only(): void
    {
        $book = new Book([
            'copyright_status' => CopyrightStatus::PublicDomain,
        ]);

        $this->assertFalse($book->hasExternalSourceOnly());

        $bookExternal = new Book([
            'copyright_status' => CopyrightStatus::ExternalSourceOnly,
        ]);

        $this->assertTrue($bookExternal->hasExternalSourceOnly());
    }
}
