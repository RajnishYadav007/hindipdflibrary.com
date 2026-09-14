# Legal Metadata & Content Rights Architecture

## 1. Overview & Policy Objective
Hindi PDF Library (`hindipdflibrary.com`) is a digital discovery and reading platform dedicated to Hindi literature, academic texts, historical documents, and open-knowledge resources. A fundamental architectural tenet is strict copyright compliance, rights transparency, and prevention of unauthorized distribution.

The platform distinguishes between **locally hostable content** and **external catalog records**. Only materials with explicit, verified legal rights are stored on server disks and offered for direct download.

---

## 2. Content Rights Classification (`CopyrightStatus`)

All titles are tagged with an explicit status enum (`App\Enums\CopyrightStatus`):

| Enum Key | Display Label | Local PDF Hosting? | Direct Download? | Read Action Behavior |
| :--- | :--- | :---: | :---: | :--- |
| `public_domain` | Public Domain | **Yes** | **Yes** | Streams local PDF or displays in embedded viewer |
| `open_access` | Open Access | **Yes** | **Yes** | Streams local PDF or displays in embedded viewer |
| `creative_commons` | Creative Commons | **Yes** | **Yes** | Streams local PDF with CC license attribution |
| `government` | Government Publication | **Yes** | **Yes** | Streams official public report/gazette |
| `permission_granted` | Express Permission Granted | **Yes** | **Yes** | Streams local PDF with logged permission notes |
| `publisher_authorized` | Publisher Authorized | **Yes** | **Yes** | Streams authorized edition under agreement |
| `external_source_only` | External Source Only | **No** | **No** (403) | Safely redirects to verified external repository (`archive.org`, etc.) |
| `unknown` | Rights Under Review | **No** | **No** (403) | Displays catalog record; download forbidden until verified |

---

## 3. Enforcement Mechanisms

### 3.1 Model-Level Authority
The `Book` model (`app/Models/Book.php`) exposes domain helpers:
- `canHostPdf(): bool`
- `canDownloadPdf(): bool`
- `hasExternalSourceOnly(): bool`
- `getSafeSourceUrl(): ?string`

### 3.2 Controller-Level Gatekeeping
In `BookController@download` (`app/Http/Controllers/BookController.php`):
```php
if (! $book->canDownloadPdf()) {
    abort(403, 'Direct download is restricted or unavailable for this title.');
}
```
Direct download attempts on `external_source_only` or `unknown` titles result in immediate `403 Forbidden` responses.

### 3.3 Safe Source URL Redirection
In `BookController@read`:
- For `external_source_only`, the controller calls `SafeUrl::sanitize($book->source_url)`.
- If valid, redirects safely with `redirect()->away($safeUrl)`.
- Rejects dangerous schemes (`javascript:`, `data:`, `file:`) and private/loopback IP addresses.

### 3.4 Public Blade Templates
In `resources/views/public/books/show.blade.php`:
- If `canDownloadPdf()` is true: displays direct PDF Download button referencing `route('books.download', ...)`.
- If `hasExternalSourceOnly()`: displays "Access at Primary Source" linking to the sanitized source URL.
- If `unknown`: displays "Rights Under Review" notice explaining that rights verification is underway.

---

## 4. Takedown & DMCA Compliance Workflow
1. **Notice Ingestion:** Copyright holders submit notices via contact form or legal email with proof of rights ownership.
2. **Immediate Administrative Action:** In Filament Admin, the book's `copyright_status` is updated to `unknown` or `is_published` is toggled off (`Draft`/`Archived`).
3. **Physical File Handling:** If contested, the PDF is moved to quarantine or unlinked from disk.
4. **Audit Trail:** Admin records notes in `permission_notes` documenting notice timestamp, claimant details, and resolution.
