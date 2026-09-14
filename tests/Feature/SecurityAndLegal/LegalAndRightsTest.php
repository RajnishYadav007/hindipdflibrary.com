<?php

namespace Tests\Feature\SecurityAndLegal;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Support\Security\SafeUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LegalAndRightsTest extends TestCase
{
    use RefreshDatabase;

    public function test_copyright_status_enum_capabilities(): void
    {
        $this->assertTrue(CopyrightStatus::PublicDomain->canHostPdf());
        $this->assertTrue(CopyrightStatus::PublicDomain->canDownloadPdf());
        $this->assertFalse(CopyrightStatus::PublicDomain->isExternalSourceOnly());

        $this->assertTrue(CopyrightStatus::CreativeCommons->canHostPdf());
        $this->assertTrue(CopyrightStatus::CreativeCommons->canDownloadPdf());

        $this->assertFalse(CopyrightStatus::ExternalSourceOnly->canHostPdf());
        $this->assertFalse(CopyrightStatus::ExternalSourceOnly->canDownloadPdf());
        $this->assertTrue(CopyrightStatus::ExternalSourceOnly->isExternalSourceOnly());

        $this->assertFalse(CopyrightStatus::Unknown->canHostPdf());
        $this->assertFalse(CopyrightStatus::Unknown->canDownloadPdf());
        $this->assertFalse(CopyrightStatus::Unknown->isExternalSourceOnly());
    }

    public function test_safe_url_utility_validates_and_rejects_dangerous_urls(): void
    {
        // Safe URLs
        $this->assertTrue(SafeUrl::isValid('https://archive.org/details/godan'));
        $this->assertTrue(SafeUrl::isValid('http://example.com/books/sample.pdf'));

        // Dangerous schemes
        $this->assertFalse(SafeUrl::isValid('javascript:alert(1)'));
        $this->assertFalse(SafeUrl::isValid('data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg=='));
        $this->assertFalse(SafeUrl::isValid('file:///etc/passwd'));
        $this->assertFalse(SafeUrl::isValid('phar://exploit.phar'));

        // Private / loopback hosts
        $this->assertFalse(SafeUrl::isValid('http://localhost/admin'));
        $this->assertFalse(SafeUrl::isValid('http://127.0.0.1:8000/secret'));
        $this->assertFalse(SafeUrl::isValid('http://169.254.169.254/latest/meta-data'));
        $this->assertFalse(SafeUrl::isValid('http://10.0.0.1/internal'));
    }

    public function test_download_denied_when_copyright_is_external_source_only(): void
    {
        $book = Book::create([
            'slug' => 'external-source-book',
            'status' => BookStatus::Published,
            'copyright_status' => CopyrightStatus::ExternalSourceOnly,
            'source_url' => 'https://archive.org/details/external-book',
        ]);

        $response = $this->get(route('books.download', ['slug' => $book->slug]));
        $response->assertForbidden();
    }

    public function test_download_denied_when_copyright_is_unknown(): void
    {
        $book = Book::create([
            'slug' => 'unknown-rights-book',
            'status' => BookStatus::Published,
            'copyright_status' => CopyrightStatus::Unknown,
        ]);

        $response = $this->get(route('books.download', ['slug' => $book->slug]));
        $response->assertForbidden();
    }

    public function test_download_streams_when_copyright_permits_and_pdf_exists(): void
    {
        Storage::fake('private_pdfs');
        $pdfContent = "%PDF-1.5\n%Dummy valid pdf content for testing";

        $book = Book::create([
            'slug' => 'godan-public-domain',
            'status' => BookStatus::Published,
            'copyright_status' => CopyrightStatus::PublicDomain,
        ]);

        Storage::disk('private_pdfs')->put("books/{$book->slug}.pdf", $pdfContent);

        BookTranslation::create([
            'book_id' => $book->id,
            'locale' => 'en',
            'title' => 'Godan',
        ]);

        $response = $this->get(route('books.download', ['slug' => $book->slug]));
        $response->assertSuccessful();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('x-content-type-options', 'nosniff');
    }

    public function test_read_redirects_to_external_source_safely_when_external_source_only(): void
    {
        $book = Book::create([
            'slug' => 'external-reading-book',
            'status' => BookStatus::Published,
            'copyright_status' => CopyrightStatus::ExternalSourceOnly,
            'source_url' => 'https://archive.org/details/external-reading-book',
        ]);

        $response = $this->get(route('books.read', ['slug' => $book->slug]));
        $response->assertRedirect('https://archive.org/details/external-reading-book');
    }
}
