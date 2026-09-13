# Workstream J: Storage & Media Architecture Strategy

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Component:** File Storage, Media Processing, and Public Asset Security  
**Auditor:** Lead Solutions Architect & Security Engineer  

---

## 1. Executive Summary & Security Directives

> [!CAUTION]
> **Zero Web Root Exposure Rule:**
> 1. Application source code, `.env`, SQLite databases, vendor files, and private documents must NEVER reside in the public web root directory.
> 2. The web server's document root must point strictly to Laravel's `/public` folder.
> 3. Uploaded PDFs, unless explicitly marked public, must be stored in private, non-web-accessible storage disks and served only through authenticated/metered streaming controllers.

---

## 2. Storage Disks & Directory Hierarchy

Laravel's `config/filesystems.php` will define four distinct storage disks:

```
storage/
├── app/
│   ├── public/                      <-- Disks: 'public' (Symlinked to public/storage)
│   │   ├── covers/                  <-- Book cover images (WebP/JPEG, optimized)
│   │   │   ├── original/
│   │   │   ├── thumb/ (200x300)
│   │   │   └── medium/ (400x600)
│   │   └── authors/                 <-- Author avatars & portraits
│   │
│   ├── private_pdfs/                <-- Disks: 'private_pdfs' (NON-PUBLIC)
│   │   └── {year}/{month}/{uuid}.pdf <-- Legally hosted PDF files (Protected)
│   │
│   └── temp/                        <-- Temporary conversion scratch space
│
└── framework/                       <-- Cache, sessions, compiled views
```

### Disk Definition Matrix:

| Disk Name | Visibility | Storage Location | Intended Use Case |
| :--- | :--- | :--- | :--- |
| **`public`** | Public (via Symlink) | `storage/app/public` | Book covers, author avatars, general imagery |
| **`private_pdfs`** | Private (Protected) | `storage/app/private_pdfs` | Copyrighted or metered digital books (PDF/EPUB) |
| **`local`** | Private (Internal) | `storage/app` | System dumps, import batches, temporary exports |
| **`s3` / `r2`** | Future Cloud Store | Cloudflare R2 / AWS S3 | Scalable cloud bucket upgrade (Phase 2+) |

---

## 3. Public Media Management: Spatie Media Library

For book covers and author avatars, `spatie/laravel-medialibrary` will be utilized with the **GD image driver**:

```php
// Book Model Media Definition
public function registerMediaConversions(Media $media = null): void
{
    $this->addMediaConversion('thumb')
        ->width(200)
        ->height(300)
        ->format('webp')
        ->quality(80)
        ->nonQueued(); // Fast synchronous thumbnail for admin preview

    $this->addMediaConversion('card')
        ->width(400)
        ->height(600)
        ->format('webp')
        ->quality(85)
        ->performOnQueue(); // Large conversion processed in background queue
}
```

---

## 4. PDF Storage, Legal Compliance, & Secure Delivery

### 4.1. File Ingestion & Security Validation
Every uploaded PDF must pass rigorous validation before persistence:
1. **MIME Type Validation:** Ensure `application/pdf` via both PHP `fileinfo` (magic bytes check) and client extension.
2. **File Size Enforcement:** Default maximum upload set to 50MB (configurable via `.env` `MAX_PDF_UPLOAD_MB=50`).
3. **Randomized UUID Filenames:** Files are renamed using cryptographic UUIDs (`e.g., 4b89f3a1-7c9d-4e1b-bc54-d890a21efc01.pdf`). Never store original uploaded filenames on disk to prevent path traversal attacks (`../../`).
4. **Legal / Source Metadata:** Every PDF record must store:
   - `license_type`: (e.g., `Public Domain`, `Creative Commons`, `Author Permitted`, `External Link Only`).
   - `copyright_owner`: Legal rights holder.
   - `source_url`: Provenance verification link.

### 4.2. Secure PDF Delivery Controller (Metered Streaming)
PDF files stored in `private_pdfs` are never directly accessible via URL. They are served through a rate-limited streaming controller:

```php
namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookDownloadController extends Controller
{
    public function download(Request $request, Book $book): StreamedResponse
    {
        // 1. Authorize access & check legal status
        abort_unless($book->is_downloadable, 403, 'This book is not available for direct download.');
        
        // 2. Increment download analytics out of band
        event(new BookDownloaded($book, $request->ip()));

        // 3. Stream securely with inline or attachment disposition
        $disk = Storage::disk('private_pdfs');
        abort_unless($disk->exists($book->pdf_path), 404);

        return $disk->download(
            $book->pdf_path,
            $book->slug . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}
```

---

## 5. Migration Path to Cloud Object Storage (Cloudflare R2 / AWS S3)

When media storage exceeds shared hosting quota (e.g., > 10GB):
1. Create a Cloudflare R2 bucket (`hindipdf-media`).
2. Update `.env`:
   ```dotenv
   FILESYSTEM_DISK=r2
   AWS_ACCESS_KEY_ID=xxx
   AWS_SECRET_ACCESS_KEY=xxx
   AWS_DEFAULT_REGION=auto
   AWS_BUCKET=hindipdf-media
   AWS_ENDPOINT=https://<account_id>.r2.cloudflarestorage.com
   ```
3. Run an Artisan sync command to copy local files to R2.
4. **Zero application code changes required.**
