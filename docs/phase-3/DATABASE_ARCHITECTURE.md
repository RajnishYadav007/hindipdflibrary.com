# Database Architecture & Core Data Model

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Phase:** Phase 3 — Database & Core Data Model  
**Architect:** Principal Database Architect & Senior Laravel Engineer  
**Date:** September 13, 2026  
**Status:** Approved & Implemented  

---

## 1. Architectural Philosophy & Scope

The database architecture for Hindi PDF Library is designed to satisfy three foundational criteria:
1. **Relational Integrity & Normalization:** High normalization ensures zero duplicate data, robust referential integrity, and predictable mutations across multilingual records.
2. **Shared Hosting Optimization (V1):** Strictly avoids daemon-dependent features, leveraging MySQL 8 InnoDB engines, composite indexes, and standard B-tree queries with low memory overhead.
3. **Frictionless Cloud/VPS Scalability (V2):** Clean Eloquent relationships and native migrations ensure seamless scale-up to Amazon Aurora, Cloudflare R2, or dedicated MySQL clusters with zero code changes.

In strict compliance with Phase 3 guidelines, **no UI or business logic creep** is permitted: no book pages, admin CRUD, search interfaces, or analytics pipelines are implemented ahead of their designated phases.

---

## 2. Key Architecture Decisions

### 2.1. Primary Key Strategy
- **`BIGINT UNSIGNED AUTO_INCREMENT` (`$table->id()`)**:
  - Consistent with native Laravel conventions across all existing tables (`users`, `sessions`, `jobs`).
  - Maximizes performance on shared hosting InnoDB buffer pools (sequential clustered index insertion).
  - Avoids UUID/ULID primary keys on entity tables, eliminating index page fragmentation and oversized foreign keys on multi-table joins.
  - Polymorphic media uses a secondary UUID (`uuid`) for obfuscated public download identifiers and CDN paths.

### 2.2. Multilingual Translation Strategy
- **Normalized Relational Translations (`*_translations`)**:
  - Translatable content is isolated into dedicated satellite tables: `book_translations`, `author_translations`, `category_translations`, and `genre_translations`.
  - Non-translatable master attributes (slugs, statuses, ISBNs, page counts, years, counters, hierarchy) remain on the parent tables.
  - Each translation table enforces a composite unique constraint: `UNIQUE(parent_id, locale)`.
  - This architecture supersedes Phase 0's exploratory JSON proposal (`ADR-007`), enabling:
    1. Direct MySQL `FULLTEXT` natural language and boolean indexing per language.
    2. Strict database-level uniqueness for localized slugs and titles.
    3. Type-safe querying without JSON extraction overhead.

### 2.3. Multi-Language Availability (`book_languages`)
- Books in the library may be published, translated, or digitized across multiple languages (e.g., Hindi, English, Sanskrit, Urdu).
- Books do **not** use a single `language_id` foreign key.
- Instead, a pivot table `book_languages` models the Many-to-Many availability of each book across language editions, with `UNIQUE(book_id, language_id)`.
- A nullable `original_language_id` is maintained on `books` strictly to preserve linguistic heritage information.

### 2.4. Controlled State Transitions (PHP 8.4 Backed Enums)
- Entity states are governed by PHP 8.4 backed enums:
  - `BookStatus`: `draft`, `published`, `archived`
  - `ReviewStatus`: `pending`, `approved`, `rejected`, `spam`
  - `AuthorRole`: `author`, `translator`, `editor`, `illustrator`
- Stored as indexed `VARCHAR(20)` / `VARCHAR(30)` in MySQL to ensure compatibility across MariaDB and MySQL without fragile database-level `ENUM` alterations.

### 2.5. Denormalized Counter Strategy
- To maintain lightning-fast response times on book listings and category grids on shared hosting:
  - `books.rating_average` (decimal 3,2)
  - `books.rating_count` (unsigned integer)
  - `books.review_count` (unsigned integer)
  - `books.view_count` (unsigned bigint)
- **Canonical Source of Truth:** The `reviews` table is the single source of truth for ratings and reviews.
- **Future Recalculation:** Denormalized counters will be maintained and periodically reconciled via dedicated Actions/Jobs in Phase 4+.

### 2.6. Anonymized Review Telemetry
- To combat review spam and automated manipulation without violating user privacy:
  - `reviews.ip_hash`: SHA-256 hashed string (64 characters) of client IP. Plaintext IP addresses are never persisted.
  - `reviews.user_agent_hash`: SHA-256 hashed client user agent string.

---

## 3. Database Engine & Character Set

| Setting | Configuration | Rationale |
| :--- | :--- | :--- |
| **Engine** | `InnoDB` | ACID compliance, row-level locking, foreign key integrity |
| **Character Set** | `utf8mb4` | Complete UTF-8 support for Devanagari Hindi and multilingual scripts |
| **Collation** | `utf8mb4_unicode_ci` | Standard Unicode sorting and case-insensitive comparison |

---

## 4. Entity-Relationship Overview

```
users ───────────┬──────────────┐
                 │ 1:M          │ 1:M
                 ▼              ▼
           favorites         reviews
                 │ M:1          │ M:1
                 └──────┬───────┘
                        ▼
                      books ────────────► book_translations (1:M)
                        │
         ┌──────────────┼──────────────┬──────────────┐
         │ M:M          │ M:M          │ M:M          │ M:M
         ▼              ▼              ▼              ▼
      authors       categories       genres       languages
         │ 1:M          │ 1:M          │ 1:M
         ▼              ▼              ▼
   author_trans   category_trans   genre_trans
```
