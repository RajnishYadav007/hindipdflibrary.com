# Legal & Source Tracking Schema Architectural Decision

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Phase:** Phase 3 — Database & Core Data Model  
**Updated:** September 13, 2026  

---

## 1. Context & Business Need

As a digital library distributing digital literature, textbooks, and cultural heritage manuscripts, legal compliance and provenance tracking are mission-critical. We must track:
- Where the source document was obtained (e.g. Internet Archive, National Library of India, private scan).
- The exact copyright and public domain status.
- The rights holder or publishing authority.
- The licensing terms (e.g. CC-BY, Public Domain, Publisher Permission).
- Specific permission notes or legal audit trails.

---

## 2. Decision: Direct Foundation Columns on `books`

We evaluated two structural options:
1. **Dedicated 1-to-1 Relational Table (`book_sources` / `book_licenses`):** Highly normalized, but creates an unnecessary join for every book query and adds administrative form complexity.
2. **Embedded Foundation Columns on `books`:** Directly integrates the 5 core provenance attributes on the parent entity.

We chose **Option 2 (Direct Foundation Columns on `books`)**:
```php
$table->string('source_url', 500)->nullable();
$table->string('license_type', 100)->nullable();
$table->string('rights_holder', 255)->nullable();
$table->string('copyright_status', 50)->nullable();
$table->text('permission_notes')->nullable();
```

---

## 3. Rationale
1. **Normalized Without Complexity:** In a digital library, every digitized book has exactly one primary copyright status and legal license. A separate 1-to-1 table introduces unnecessary joins with zero relational benefit.
2. **Admin Panel Ergonomics:** In Filament 5, these fields map directly into a clean "Legal & Provenance" section in the book editor without requiring a nested relation manager.
3. **Future Extension Path:** If Phase 6+ requires multi-file provenance (e.g., individual licenses per scanned edition or volume), a dedicated `book_files` table can be introduced cleanly without altering these master attributes.
