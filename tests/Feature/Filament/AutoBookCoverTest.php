<?php

namespace Tests\Feature\Filament;

use App\Models\Book;
use App\Models\Language;
use App\Services\Media\BookCoverService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AutoBookCoverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('private_pdfs');
    }

    public function test_auto_generate_cover_extracts_from_pdf_stream(): void
    {
        $lang = Language::create(['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी', 'is_active' => true]);
        $book = Book::create([
            'title' => 'Sample Hindi PDF Book',
            'slug' => 'sample-hindi-pdf-book',
            'original_language_id' => $lang->id,
            'is_active' => true,
            'is_featured' => false,
            'status' => 'published',
        ]);

        // Create a dummy PDF with an embedded JPEG stream
        $fakeImg = imagecreatetruecolor(200, 300);
        $bg = imagecolorallocate($fakeImg, 200, 50, 50);
        imagefilledrectangle($fakeImg, 0, 0, 200, 300, $bg);
        ob_start();
        imagejpeg($fakeImg);
        $fakeJpgBytes = ob_get_clean();
        imagedestroy($fakeImg);

        // PDF dummy header + embedded JPEG stream + PDF dummy footer
        $fakePdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /XObject /Subtype /Image /Filter /DCTDecode >>\nstream\n"
            .$fakeJpgBytes
            ."\nendstream\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF";

        Storage::disk('private_pdfs')->put('sample_test.pdf', $fakePdfContent);

        /** @var BookCoverService $service */
        $service = app(BookCoverService::class);
        $extractedCover = $service->autoGenerateCover($book, 'sample_test.pdf');

        $this->assertNotNull($extractedCover);
        $this->assertStringStartsWith('covers/cover_sample-hindi-pdf-book_', $extractedCover);
        Storage::disk('public')->assertExists($extractedCover);
    }

    public function test_auto_generate_cover_falls_back_to_typographic_cover_for_text_pdf(): void
    {
        $lang = Language::create(['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी', 'is_active' => true]);
        $book = Book::create([
            'title' => 'Pure Text Heritage Book',
            'slug' => 'pure-text-heritage-book',
            'original_language_id' => $lang->id,
            'is_active' => true,
            'is_featured' => false,
            'status' => 'published',
        ]);

        // Create a PDF without images
        $fakePdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n%%EOF";
        Storage::disk('private_pdfs')->put('text_only.pdf', $fakePdfContent);

        /** @var BookCoverService $service */
        $service = app(BookCoverService::class);
        $cover = $service->autoGenerateCover($book, 'text_only.pdf');

        $this->assertNotNull($cover);
        $this->assertStringStartsWith('covers/generated_pure-text-heritage-book_', $cover);
        Storage::disk('public')->assertExists($cover);
    }
}
