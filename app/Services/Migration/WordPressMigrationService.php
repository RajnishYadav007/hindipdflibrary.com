<?php

namespace App\Services\Migration;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Redirect;
use App\Support\Security\SafeUrl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WordPressMigrationService
{
    /**
     * Validate and sanitize raw WordPress book/post data.
     *
     * @param  array<string, mixed>  $raw
     * @return array{valid: bool, errors: array<string>, warnings: array<string>, data: array<string, mixed>}
     */
    public function validateBookData(array $raw): array
    {
        $errors = [];
        $warnings = [];
        $data = [];

        // Title validation
        $title = trim((string) ($raw['title'] ?? ''));
        if ($title === '') {
            $errors[] = 'Title is required.';
        } else {
            $data['title'] = $title;
        }

        // Slug validation
        $slug = trim((string) ($raw['slug'] ?? ''));
        if ($slug === '' && $title !== '') {
            $slug = Str::slug($title);
        }

        if ($slug === '') {
            $errors[] = 'Slug cannot be determined.';
        } else {
            $slug = Str::slug($slug);
            $data['slug'] = $slug;

            // Check if slug already exists
            $existing = Book::where('slug', $slug)->first();
            if ($existing) {
                $warnings[] = "Slug '{$slug}' already exists in database (Book ID: {$existing->id}).";
            }
        }

        // Copyright status validation
        $rawStatus = (string) ($raw['copyright_status'] ?? 'unknown');
        $status = CopyrightStatus::tryFrom($rawStatus);
        if (! $status) {
            $warnings[] = "Unrecognized copyright status '{$rawStatus}', defaulting to 'unknown'.";
            $status = CopyrightStatus::Unknown;
        }
        $data['copyright_status'] = $status;

        // Source URL validation
        $sourceUrl = trim((string) ($raw['source_url'] ?? ''));
        if ($sourceUrl !== '') {
            if (! SafeUrl::isValid($sourceUrl)) {
                $errors[] = "Dangerous or invalid source URL scheme/host: '{$sourceUrl}'.";
                $data['source_url'] = null;
            } else {
                $data['source_url'] = $sourceUrl;
            }
        } else {
            $data['source_url'] = null;
        }

        // External source only requires a valid source URL
        if ($status === CopyrightStatus::ExternalSourceOnly && empty($data['source_url'])) {
            $errors[] = "Copyright status 'external_source_only' requires a valid source_url.";
        }

        // Optional metadata
        $data['description'] = isset($raw['description']) ? trim((string) $raw['description']) : null;
        $data['license_type'] = isset($raw['license_type']) ? trim((string) $raw['license_type']) : null;
        $data['rights_holder'] = isset($raw['rights_holder']) ? trim((string) $raw['rights_holder']) : null;
        $data['permission_notes'] = isset($raw['permission_notes']) ? trim((string) $raw['permission_notes']) : null;
        $data['is_published'] = (bool) ($raw['is_published'] ?? true);
        $data['published_at'] = $raw['published_at'] ?? now();
        $data['language_code'] = ! empty($raw['language_code']) ? strtolower(trim((string) $raw['language_code'])) : 'hi';

        // Legacy path for redirect
        $legacyPath = trim((string) ($raw['legacy_path'] ?? ''));
        if ($legacyPath !== '') {
            $parsed = parse_url($legacyPath, PHP_URL_PATH) ?? $legacyPath;
            $data['legacy_path'] = '/'.trim($parsed, '/');
        } else {
            $data['legacy_path'] = null;
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'data' => $data,
        ];
    }

    /**
     * Create or update a 301/302 redirect for legacy URLs.
     */
    public function generateRedirect(string $legacyPath, string $newPath, int $statusCode = 301): Redirect
    {
        $cleanSource = trim($legacyPath, '/');
        $normalizedSource = '/'.$cleanSource;

        $cleanDest = trim($newPath, '/');
        $normalizedDest = '/'.$cleanDest;

        return Redirect::updateOrCreate(
            ['source_path' => $normalizedSource],
            [
                'destination_path' => $normalizedDest,
                'status_code' => $statusCode,
                'is_active' => true,
            ]
        );
    }

    /**
     * Import a single validated book record into the database.
     *
     * @param  array<string, mixed>  $validatedData
     */
    public function importBook(array $validatedData, bool $dryRun = false): ?Book
    {
        if ($dryRun) {
            return null;
        }

        return DB::transaction(function () use ($validatedData) {
            $book = Book::firstOrNew(['slug' => $validatedData['slug']]);

            $book->status = ! empty($validatedData['is_published'])
                ? BookStatus::Published
                : BookStatus::Draft;
            $book->copyright_status = $validatedData['copyright_status'];
            $book->source_url = $validatedData['source_url'] ?? null;
            $book->license_type = $validatedData['license_type'] ?? null;
            $book->rights_holder = $validatedData['rights_holder'] ?? null;
            $book->permission_notes = $validatedData['permission_notes'] ?? null;

            $book->save();

            // Setup translation for the book
            $locale = $validatedData['language_code'] ?? 'hi';
            BookTranslation::updateOrCreate(
                [
                    'book_id' => $book->id,
                    'locale' => $locale,
                ],
                [
                    'title' => $validatedData['title'],
                    'description' => $validatedData['description'] ?? null,
                ]
            );

            // Also ensure fallback English translation exists if imported in Hindi
            if ($locale !== 'en') {
                BookTranslation::firstOrCreate(
                    [
                        'book_id' => $book->id,
                        'locale' => 'en',
                    ],
                    [
                        'title' => $validatedData['title'],
                        'description' => $validatedData['description'] ?? null,
                    ]
                );
            }

            // Setup 301 redirect if legacy path was provided
            if (! empty($validatedData['legacy_path'])) {
                $this->generateRedirect($validatedData['legacy_path'], '/books/'.$book->slug, 301);
            }

            return $book;
        });
    }
}
