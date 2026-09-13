# AI Engineering Guidelines & Operational Standards (AGENTS.md)

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Application Type:** Digital Book/Library Discovery & Content Platform  
**Target Stack:** Laravel 13, PHP 8.4+, MySQL 8, Livewire 4, Filament 5, Tailwind CSS  
**Target Environment:** Shared Hosting (StackCP/ServerByt) V1 ➔ VPS/Cloud V2  

---

## 1. Mandatory Instructions for All AI Agents

> [!IMPORTANT]
> **EVERY AI coding agent working on this repository MUST read and adhere strictly to this file before creating, modifying, or executing any code or commands.**

### The 5 Cardinal Rules:
1. **INSPECT BEFORE IMPLEMENTING:** Always check existing files, schema, and tests before creating new ones.
2. **VERIFY BEFORE ASSUMING:** Never assume infrastructure capabilities (e.g., Redis, persistent workers, Meilisearch) exist unless confirmed in `docs/architecture/`.
3. **PRODUCTION SAFETY FIRST:** Never run destructive commands (`rm -rf`, `DROP DATABASE`, `migrate:fresh`, `git reset --hard`) without explicit, documented user authorization.
4. **NO OVER-ENGINEERING:** Prefer native Laravel conventions and built-in features over unnecessary third-party packages or complex microservices.
5. **SEO & MULTILINGUAL FIRST:** Hindi PDF Library is an SEO-heavy digital library. Every public URL, slug, meta tag, and database query must respect English/Hindi multilingual SEO.

---

## 2. Architecture Principles

1. **Shared-Hosting Compatible V1:**
   - No daemon requirements (no Supervisor, no background workers running infinitely).
   - Queue jobs must use `QUEUE_CONNECTION=database` with `--stop-when-empty` cron execution.
   - Cache must use `CACHE_STORE=file` or `CACHE_STORE=database` via Laravel's Cache facade. No direct `Redis::` calls.
   - Search must use MySQL 8 `FULLTEXT` natural language boolean queries in V1.
2. **Clean Abstractions:**
   - Always code against Laravel interfaces (`Illuminate\Contracts\...`).
   - When VPS/Cloud migration occurs, changing `.env` must be sufficient with zero code refactoring.
3. **Database-Driven Multilingual Architecture:**
   - Support English and Hindi (Devanagari script) seamlessly.
   - Titles and authors must store both original and transliterated phonetic text for search indexing.

---

## 3. Laravel Coding & Naming Conventions

1. **Models & Schema:**
   - Singular PascalCase for models (`Book`, `Author`, `Category`).
   - Plural snake_case for tables (`books`, `authors`, `categories`).
   - Foreign keys: `singular_model_id` (`author_id`, `category_id`).
   - Always declare `$fillable` or `$guarded = []` explicitly.
   - Always declare type hints and return types on model relationships (`public function author(): BelongsTo`).
2. **Controllers & Livewire Components:**
   - Single-responsibility controllers (`BookController`, `BookDownloadController`).
   - Livewire components reside in `app/Livewire/` (e.g., `App\Livewire\BookSearch`, `App\Livewire\ReviewForm`).
   - Use Form Requests for request validation (`StoreBookRequest`).
3. **Filament Resources:**
   - Reside in `app/Filament/Resources/`.
   - Never put heavy business logic in resource files; delegate to dedicated services or action classes.

---

## 4. Migration Rules

1. **Non-Destructive Migrations:**
   - Migrations in production must **NEVER** drop columns or tables without a staged deprecation plan.
   - `migrate:fresh` is strictly forbidden in production or staging.
2. **Character Set & Collation:**
   - Every text column supporting Hindi must use `utf8mb4` with `utf8mb4_unicode_ci` or `utf8mb4_0900_ai_ci`.
3. **Rollback Integrity:**
   - Every `up()` method must have a clean, reversible `down()` method.

---

## 5. Security & Input Validation Rules

1. **Authentication & Authorization:**
   - Every controller and Livewire action modifying data must authorize via Policy or Spatie permission check (`$this->authorize(...)`).
2. **Raw Queries Forbidden:**
   - Direct SQL interpolation (`DB::raw("WHERE title = '$search'")`) is strictly prohibited. Use PDO parameter binding.
3. **File Upload Hardening:**
   - Always validate MIME types via PHP `finfo` magic bytes (`application/pdf`, `image/webp`).
   - Always generate cryptographic UUID filenames. Never trust client-supplied filenames.
   - Private PDFs must be stored in `storage/app/private_pdfs/` outside public web root.

---

## 6. SEO & Performance Rules

1. **URLs & Slugs:**
   - Slugs must be clean, lowercase, hyphen-delimited ASCII strings (transliterated from Hindi if needed).
   - URLs must be canonicalized with proper `<link rel="canonical">` and `hreflang` tags.
2. **Structured Data:**
   - Every book page must output Schema.org `Book` JSON-LD markup including: title, author, isbn, numberOfPages, inLanguage, and aggregateRating.
3. **N+1 Query Prevention:**
   - Always eager-load relationships (`Book::with(['author', 'category', 'media'])`).
   - Enable `Model::preventLazyLoading(! app()->isProduction())` in local development.

---

## 7. Package & Dependency Rules

1. **Approval Required:**
   - Do not install new Composer or NPM packages without documenting rationale in `docs/architecture/PACKAGE_COMPATIBILITY_MATRIX.md`.
2. **No Nulled / Abandoned Packages:**
   - Only official, MIT/BSD-licensed, actively maintained packages are permitted.

---

## 8. Definition of Done (DoD)

Before declaring any feature or workstream complete:
1. Code follows PSR-12 formatting (`php artisan pint` or `vendor/bin/pint`).
2. Automated tests pass (`php artisan test`).
3. Documentation updated in `docs/`.
4. No console errors or unresolved PHP deprecation warnings.
5. Production safety verified (no secrets committed, no hardcoded URLs).
