<?php

namespace Tests\Feature\Filament;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Filament\Resources\Authors\Concerns\HandlesAuthorMedia;
use App\Filament\Resources\Books\Concerns\HandlesBookMedia;
use App\Models\Author;
use App\Models\Book;
use App\Models\Media;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BookAdminMediaUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_admin_book_edit_page_renders_cover_and_pdf_upload_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole('Super Admin');

        $book = Book::factory()->create([
            'slug' => 'godaan-admin-test',
            'status' => BookStatus::Published,
        ]);

        $response = $this->actingAs($admin)->get("/admin/books/{$book->id}/edit");

        $response->assertSuccessful();
        $response->assertSee('Book Cover Image');
        $response->assertSee('Upload Book PDF');
        $response->assertSee('Copyright &amp; Hosting Rights', false);
    }

    public function test_handles_book_media_trait_syncs_cover_and_pdf_media(): void
    {
        Storage::fake('public');
        Storage::fake('private_pdfs');

        Storage::disk('public')->put('covers/test_cover.jpg', 'fake-image-bytes');
        Storage::disk('private_pdfs')->put('secure_book.pdf', '%PDF-1.4 fake-pdf');

        $book = Book::factory()->create([
            'slug' => 'test-book-sync',
            'status' => BookStatus::Published,
            'copyright_status' => CopyrightStatus::PublicDomain,
        ]);

        $handler = new class
        {
            use HandlesBookMedia {
                syncBookMedia as public;
                populateBookMedia as public;
            }
        };

        // Sync media
        $handler->syncBookMedia($book, 'covers/test_cover.jpg', 'secure_book.pdf');

        // Check Cover Media
        $coverMedia = Media::where('model_type', Book::class)
            ->where('model_id', $book->id)
            ->where('collection_name', 'cover')
            ->first();

        $this->assertNotNull($coverMedia);
        $this->assertEquals('covers/test_cover.jpg', $coverMedia->file_name);
        $this->assertEquals('public', $coverMedia->disk);

        // Check PDF Media
        $pdfMedia = Media::where('model_type', Book::class)
            ->where('model_id', $book->id)
            ->where('collection_name', 'pdf')
            ->first();

        $this->assertNotNull($pdfMedia);
        $this->assertEquals('secure_book.pdf', $pdfMedia->file_name);
        $this->assertEquals('private_pdfs', $pdfMedia->disk);
        $this->assertEquals('application/pdf', $pdfMedia->mime_type);

        // Verify Book helper methods
        $this->assertTrue($book->canHostPdf());
        $this->assertTrue($book->canDownloadPdf());
        $this->assertStringContainsString('covers/test_cover.jpg', $book->cover_url);

        // Test form pre-population
        $populatedData = $handler->populateBookMedia($book, []);
        $this->assertEquals('covers/test_cover.jpg', $populatedData['cover_image']);
        $this->assertEquals('secure_book.pdf', $populatedData['pdf_file']);

        // Test deletion on empty path
        $handler->syncBookMedia($book, null, null);
        $this->assertDatabaseMissing('media', ['id' => $coverMedia->id]);
        $this->assertDatabaseMissing('media', ['id' => $pdfMedia->id]);
    }

    public function test_book_cover_url_falls_back_to_archive_org_services_img(): void
    {
        $book = Book::factory()->create([
            'slug' => 'archive-cover-fallback',
            'source_url' => 'https://archive.org/details/godaan-premchand',
        ]);

        $this->assertEquals('https://archive.org/services/img/godaan-premchand', $book->cover_url);
    }

    public function test_handles_author_media_trait_syncs_portrait_media(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('authors/premchand.jpg', 'fake-portrait-bytes');

        $author = Author::create([
            'slug' => 'munshi-premchand-admin',
            'is_active' => true,
        ]);

        $handler = new class
        {
            use HandlesAuthorMedia {
                syncAuthorMedia as public;
                populateAuthorMedia as public;
            }
        };

        // Sync portrait
        $handler->syncAuthorMedia($author, 'authors/premchand.jpg');

        $portraitMedia = Media::where('model_type', Author::class)
            ->where('model_id', $author->id)
            ->where('collection_name', 'portrait')
            ->first();

        $this->assertNotNull($portraitMedia);
        $this->assertEquals('authors/premchand.jpg', $portraitMedia->file_name);
        $this->assertEquals('public', $portraitMedia->disk);

        // Test form pre-population
        $data = $handler->populateAuthorMedia($author, []);
        $this->assertEquals('authors/premchand.jpg', $data['portrait_image']);

        // Test removal on empty
        $handler->syncAuthorMedia($author, null);
        $this->assertDatabaseMissing('media', ['id' => $portraitMedia->id]);
    }
}
