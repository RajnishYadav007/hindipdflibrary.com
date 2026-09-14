# Phase 12 WordPress Migration Plan & Playbook

## 1. Migration Goals & Constraints
The WordPress migration process transitions legacy content from the existing WordPress instance into Hindi PDF Library without data loss, downtime, or SEO penalty.

### Key Objectives:
1. Preserve 100% of organic search rankings through exact 301 permalink mapping.
2. Cleanse and validate legacy post data, removing malformed HTML and unsafe external URLs.
3. Classify copyright status truthfully before assigning downloadable status.
4. Execute non-destructively in dry-run and live modes.

---

## 2. Architecture & Components

### 2.1 `WordPressMigrationService` (`app/Services/Migration/WordPressMigrationService.php`)
- **Validation Engine:** `validateBookData(array $raw): array`
  - Validates title, generates clean kebab slug.
  - Detects existing slug collisions and reports warnings.
  - Enforces enum compliance for `copyright_status`.
  - Audits `source_url` with `SafeUrl::isValid()`, rejecting dangerous schemes (`javascript:`, `data:`) and private IP ranges.
  - Ensures `external_source_only` titles include a valid source URL.
- **Permalink Redirect Mapping:** `generateRedirect(string $legacyPath, string $newPath, int $statusCode = 301): Redirect`
  - Maps legacy permalinks (e.g. `/godan-novel/`, `/?p=412`) into the `redirects` table.
  - Trims extraneous slashes and sets `is_active = true`.
- **Database Importer:** `importBook(array $validatedData, bool $dryRun = false): ?Book`
  - Wraps database operations in a transaction.
  - Creates/updates `Book` and localized `BookTranslation` records.
  - Creates 301 redirect entry pointing to `/books/{slug}`.

### 2.2 Artisan Import Command (`app/Console/Commands/WordPressImportCommand.php`)
Command signature:
```bash
php artisan wordpress:import {file} {--dry-run} {--validate-only}
```

Supports JSON and CSV exports.

---

## 3. Step-by-Step Execution Playbook

### Step 1: Export Legacy WordPress Data
Export WordPress posts and metadata using WP CLI or standard export tools to JSON:
```json
[
  {
    "title": "गोदान (Godan)",
    "slug": "godan-premchand",
    "copyright_status": "public_domain",
    "source_url": "https://archive.org/details/godan",
    "legacy_path": "/godan-novel/"
  }
]
```

### Step 2: Dry Run Validation
Execute validation without writing to the database:
```bash
php artisan wordpress:import storage/imports/wordpress_export.json --dry-run
```
Review output metrics: valid records, invalid records, warnings, and error table.

### Step 3: Live Import
Execute live migration:
```bash
php artisan wordpress:import storage/imports/wordpress_export.json
```

### Step 4: Verification & SEO Check
- Confirm book records in Filament Admin (`/admin/books`).
- Test legacy URL redirection: requesting `https://hindipdflibrary.com/godan-novel/` returns `301 Moved Permanently` to `https://hindipdflibrary.com/books/godan-premchand`.
- Inspect hit counts in `redirects` table to monitor legacy traffic migration.
