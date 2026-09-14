<?php

namespace Tests\Feature\SecurityAndLegal;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrivatePdfStorageTest extends TestCase
{
    use RefreshDatabase;

    public function test_private_pdfs_are_not_accessible_via_public_storage_url(): void
    {
        // Public storage URL for private_pdfs should 403 or 404
        $response = $this->get('/storage/private_pdfs/secret.pdf');
        $this->assertContains($response->getStatusCode(), [403, 404]);
    }

    public function test_draft_book_pdf_cannot_be_downloaded(): void
    {
        Storage::fake('private_pdfs');
        Storage::disk('private_pdfs')->put('books/draft.pdf', "%PDF-1.4\nDraft book content");

        $book = Book::create([
            'slug' => 'draft-book',
            'status' => BookStatus::Draft,
            'copyright_status' => CopyrightStatus::PublicDomain,
            'pdf_path' => 'books/draft.pdf',
        ]);

        $response = $this->get(route('books.download', ['slug' => $book->slug]));
        $response->assertNotFound();
    }

    public function test_missing_pdf_file_returns_404(): void
    {
        $book = Book::create([
            'slug' => 'missing-pdf-book',
            'status' => BookStatus::Published,
            'copyright_status' => CopyrightStatus::PublicDomain,
            'pdf_path' => 'books/non_existent_file.pdf',
        ]);

        $response = $this->get(route('books.download', ['slug' => $book->slug]));
        $response->assertNotFound();
    }

    public function test_non_pdf_file_is_rejected_on_download(): void
    {
        Storage::fake('private_pdfs');
        // File with malicious or non-PDF content
        Storage::disk('private_pdfs')->put('books/malicious.pdf', '<html><body>Not a PDF</body></html>');

        $book = Book::create([
            'slug' => 'fake-pdf-book',
            'status' => BookStatus::Published,
            'copyright_status' => CopyrightStatus::PublicDomain,
            'pdf_path' => 'books/malicious.pdf',
        ]);

        $response = $this->get(route('books.download', ['slug' => $book->slug]));
        // Should return 415 or 404 due to mime type check failure
        $this->assertContains($response->getStatusCode(), [404, 415]);
    }
}
