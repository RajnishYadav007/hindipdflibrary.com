<?php

namespace Tests\Feature\SecurityAndLegal;

use App\Enums\CopyrightStatus;
use App\Models\Redirect;
use App\Services\Migration\WordPressMigrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordPressMigrationValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_migration_service_validates_clean_book_data(): void
    {
        $service = new WordPressMigrationService;

        $raw = [
            'title' => 'Nirmala',
            'slug' => 'nirmala',
            'copyright_status' => 'public_domain',
            'source_url' => 'https://archive.org/details/nirmala',
            'legacy_path' => '/nirmala-hindi-novel/',
        ];

        $result = $service->validateBookData($raw);

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
        $this->assertEquals('Nirmala', $result['data']['title']);
        $this->assertEquals('nirmala', $result['data']['slug']);
        $this->assertEquals(CopyrightStatus::PublicDomain, $result['data']['copyright_status']);
        $this->assertEquals('/nirmala-hindi-novel', $result['data']['legacy_path']);
    }

    public function test_migration_service_flags_missing_title_and_unsafe_urls(): void
    {
        $service = new WordPressMigrationService;

        $raw = [
            'title' => '',
            'copyright_status' => 'external_source_only',
            'source_url' => 'javascript:alert("hacked")',
        ];

        $result = $service->validateBookData($raw);

        $this->assertFalse($result['valid']);
        $this->assertContains('Title is required.', $result['errors']);
        $this->assertContains("Dangerous or invalid source URL scheme/host: 'javascript:alert(\"hacked\")'.", $result['errors']);
        $this->assertContains("Copyright status 'external_source_only' requires a valid source_url.", $result['errors']);
    }

    public function test_migration_service_generates_redirect(): void
    {
        $service = new WordPressMigrationService;

        $redirect = $service->generateRedirect('legacy-book-link/', '/books/new-book-link');

        $this->assertInstanceOf(Redirect::class, $redirect);
        $this->assertEquals('/legacy-book-link', $redirect->source_path);
        $this->assertEquals('/books/new-book-link', $redirect->destination_path);
        $this->assertEquals(301, $redirect->status_code);
        $this->assertTrue($redirect->is_active);
    }

    public function test_migration_service_imports_book_and_sets_up_redirect(): void
    {
        $service = new WordPressMigrationService;

        $raw = [
            'title' => 'Gaban',
            'slug' => 'gaban',
            'copyright_status' => 'public_domain',
            'legacy_path' => '/premchand-gaban-novel/',
        ];

        $validated = $service->validateBookData($raw);
        $book = $service->importBook($validated['data'], false);

        $this->assertNotNull($book);
        $this->assertDatabaseHas('books', ['slug' => 'gaban']);
        $this->assertDatabaseHas('book_translations', ['book_id' => $book->id, 'title' => 'Gaban']);
        $this->assertDatabaseHas('redirects', [
            'source_path' => '/premchand-gaban-novel',
            'destination_path' => '/books/gaban',
            'status_code' => 301,
        ]);
    }

    public function test_migration_command_executes_with_json_export(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'wp_').'.json';
        file_put_contents($tempFile, json_encode([
            [
                'title' => 'Test Imported Book 1',
                'slug' => 'test-imported-book-1',
                'copyright_status' => 'public_domain',
                'legacy_path' => '/wp-post-1/',
            ],
            [
                'title' => 'Test Imported Book 2',
                'slug' => 'test-imported-book-2',
                'copyright_status' => 'external_source_only',
                'source_url' => 'https://archive.org/book2',
                'legacy_path' => '/wp-post-2/',
            ],
        ]));

        $this->artisan('wordpress:import', ['file' => $tempFile, '--dry-run' => true])
            ->assertSuccessful();

        // Under dry run, books are not saved to database
        $this->assertDatabaseMissing('books', ['slug' => 'test-imported-book-1']);

        // Now run live import
        $this->artisan('wordpress:import', ['file' => $tempFile])
            ->assertSuccessful();

        $this->assertDatabaseHas('books', ['slug' => 'test-imported-book-1']);
        $this->assertDatabaseHas('book_translations', ['title' => 'Test Imported Book 1']);
        $this->assertDatabaseHas('redirects', ['source_path' => '/wp-post-1', 'destination_path' => '/books/test-imported-book-1']);

        @unlink($tempFile);
    }
}
