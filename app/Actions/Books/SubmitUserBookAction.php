<?php

namespace App\Actions\Books;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Models\Author;
use App\Models\AuthorTranslation;
use App\Models\Book;
use App\Models\BookTranslation;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubmitUserBookAction
{
    /**
     * Execute the book submission mutation.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, array $data, ?UploadedFile $pdfFile = null, ?UploadedFile $coverFile = null): Book
    {
        return DB::transaction(function () use ($user, $data, $pdfFile, $coverFile) {
            // 1. Resolve or create Author
            $authorId = $data['author_id'] ?? null;
            if (! $authorId && ! empty($data['new_author_name'])) {
                $authorName = trim($data['new_author_name']);
                $authorSlug = Str::slug($authorName);
                if (empty($authorSlug)) {
                    $authorSlug = 'author-'.Str::random(8);
                }

                // Ensure unique author slug
                $existingAuthor = Author::where('slug', $authorSlug)->first();
                if ($existingAuthor) {
                    $authorId = $existingAuthor->id;
                } else {
                    $author = Author::create([
                        'slug' => $authorSlug,
                        'is_active' => true,
                    ]);
                    AuthorTranslation::create([
                        'author_id' => $author->id,
                        'locale' => 'hi',
                        'name' => $authorName,
                    ]);
                    $authorId = $author->id;
                }
            }

            // 2. Generate unique slug for the Book
            $baseTitle = ! empty($data['title_en']) ? $data['title_en'] : $data['title'];
            $slugBase = Str::slug($baseTitle);
            if (empty($slugBase)) {
                $slugBase = 'book-'.Str::random(8);
            }

            $slug = $slugBase;
            $counter = 1;
            while (Book::where('slug', $slug)->exists()) {
                $slug = "{$slugBase}-{$counter}";
                $counter++;
            }

            // 3. Create Book record
            // Admin submissions can be auto-published, user submissions default to Draft (pending review)
            $status = $user->is_admin
                ? ($data['status'] ?? BookStatus::Draft->value)
                : BookStatus::Draft->value;

            $copyrightStatus = ! empty($data['copyright_status'])
                ? CopyrightStatus::tryFrom($data['copyright_status']) ?? CopyrightStatus::PublicDomain
                : CopyrightStatus::PublicDomain;

            $book = Book::create([
                'user_id' => $user->id,
                'slug' => $slug,
                'status' => $status,
                'publication_year' => ! empty($data['publication_year']) ? (int) $data['publication_year'] : null,
                'publisher' => ! empty($data['publisher']) ? substr($data['publisher'], 0, 150) : null,
                'isbn' => ! empty($data['isbn']) ? substr($data['isbn'], 0, 20) : null,
                'edition' => ! empty($data['edition']) ? substr($data['edition'], 0, 50) : null,
                'pages' => ! empty($data['pages']) ? (int) $data['pages'] : null,
                'original_language_id' => ! empty($data['original_language_id']) ? (int) $data['original_language_id'] : null,
                'source_url' => ! empty($data['source_url']) ? $data['source_url'] : null,
                'license_type' => ! empty($data['license_type']) ? $data['license_type'] : 'Public Domain',
                'copyright_status' => $copyrightStatus,
                'permission_notes' => ! empty($data['permission_notes']) ? $data['permission_notes'] : 'Submitted by community reader '.$user->name,
            ]);

            // 4. Create Book Translations
            BookTranslation::create([
                'book_id' => $book->id,
                'locale' => 'hi',
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);

            if (! empty($data['title_en'])) {
                BookTranslation::create([
                    'book_id' => $book->id,
                    'locale' => 'en',
                    'title' => $data['title_en'],
                    'description' => $data['description_en'] ?? $data['description'] ?? null,
                ]);
            }

            // 5. Attach Author
            if ($authorId) {
                $book->authors()->syncWithoutDetaching([
                    $authorId => ['role' => 'author'],
                ]);
            }

            // 6. Attach Category
            if (! empty($data['category_id'])) {
                $book->categories()->syncWithoutDetaching([(int) $data['category_id']]);
            }

            // 7. Attach Language
            if (! empty($data['original_language_id'])) {
                $book->languages()->syncWithoutDetaching([(int) $data['original_language_id']]);
            }

            // 8. Handle PDF File Upload securely
            if ($pdfFile && $pdfFile->isValid()) {
                $uuid = Str::uuid()->toString();
                $storedFileName = $uuid.'.pdf';
                $pdfFile->storeAs('', $storedFileName, 'private_pdfs');

                Media::create([
                    'model_type' => Book::class,
                    'model_id' => $book->id,
                    'uuid' => $uuid,
                    'collection_name' => 'pdf',
                    'name' => pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME),
                    'file_name' => $storedFileName,
                    'mime_type' => 'application/pdf',
                    'disk' => 'private_pdfs',
                    'size' => $pdfFile->getSize(),
                ]);
            }

            // 9. Handle Cover Image Upload securely
            if ($coverFile && $coverFile->isValid()) {
                $ext = $coverFile->guessExtension() ?: 'jpg';
                $coverUuid = Str::uuid()->toString();
                $coverFileName = 'covers/'.$coverUuid.'.'.$ext;
                $coverFile->storeAs('', $coverFileName, 'public');

                Media::create([
                    'model_type' => Book::class,
                    'model_id' => $book->id,
                    'uuid' => $coverUuid,
                    'collection_name' => 'cover',
                    'name' => pathinfo($coverFile->getClientOriginalName(), PATHINFO_FILENAME),
                    'file_name' => $coverFileName,
                    'mime_type' => $coverFile->getMimeType() ?: 'image/jpeg',
                    'disk' => 'public',
                    'size' => $coverFile->getSize(),
                ]);
            }

            return $book;
        });
    }
}
