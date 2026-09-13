# Database Schema Reference

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Phase:** Phase 3 — Database & Core Data Model  
**Updated:** September 13, 2026  

---

## 1. Table: `users`
*Purpose: Authentication and user accounts.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `name` | VARCHAR(255) | No | None | No | No | None | User display name |
| `email` | VARCHAR(255) | No | None | Yes | Yes | None | Unique login email |
| `email_verified_at` | TIMESTAMP | Yes | NULL | No | No | None | Email verification timestamp |
| `password` | VARCHAR(255) | No | None | No | No | None | Bcrypt hashed password |
| `is_admin` | TINYINT(1) | No | 0 | No | No | None | Filament admin access flag |
| `remember_token` | VARCHAR(100) | Yes | NULL | No | No | None | Remember me session token |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Record creation timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Record update timestamp |

---

## 2. Table: `languages`
*Purpose: Supported languages for books and translations.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `code` | VARCHAR(10) | No | None | Yes | Yes | None | ISO 639-1 / BCP 47 code (en, hi) |
| `name` | VARCHAR(100) | No | None | No | No | None | English name (Hindi, Sanskrit) |
| `native_name` | VARCHAR(100) | No | None | No | No | None | Native name (हिन्दी, संस्कृतम्) |
| `is_active` | TINYINT(1) | No | 1 | Yes | No | None | Public availability toggle |
| `sort_order` | SMALLINT UNSIGNED | No | 0 | No | No | None | UI sorting order |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |

---

## 3. Table: `authors`
*Purpose: Master author entities.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `slug` | VARCHAR(150) | No | None | Yes | Yes | None | SEO canonical slug |
| `is_active` | TINYINT(1) | No | 1 | Yes | No | None | Visibility toggle |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `deleted_at` | TIMESTAMP | Yes | NULL | No | No | None | Soft delete timestamp |

---

## 4. Table: `author_translations`
*Purpose: Localized author profiles and biographies.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `author_id` | BIGINT UNSIGNED | No | None | Yes | No | `authors.id` | CASCADE ON DELETE |
| `locale` | VARCHAR(10) | No | None | Yes | No | None | Locale code (en, hi) |
| `name` | VARCHAR(150) | No | None | No | No | None | Localized author name |
| `biography` | TEXT | Yes | NULL | No | No | None | Localized biography |
| `seo_title` | VARCHAR(255) | Yes | NULL | No | No | None | Editorial SEO title |
| `seo_description` | VARCHAR(500) | Yes | NULL | No | No | None | Editorial SEO description |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |

*Constraint:* `UNIQUE(author_id, locale)`

---

## 5. Table: `categories`
*Purpose: Hierarchical library taxonomy.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `parent_id` | BIGINT UNSIGNED | Yes | NULL | Yes | No | `categories.id` | NULL ON DELETE |
| `slug` | VARCHAR(150) | No | None | Yes | Yes | None | SEO URL slug |
| `is_active` | TINYINT(1) | No | 1 | Yes | No | None | Active status |
| `sort_order` | SMALLINT UNSIGNED | No | 0 | No | No | None | Navigation ordering |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `deleted_at` | TIMESTAMP | Yes | NULL | No | No | None | Soft delete timestamp |

---

## 6. Table: `category_translations`
*Purpose: Localized category titles and descriptions.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `category_id` | BIGINT UNSIGNED | No | None | Yes | No | `categories.id` | CASCADE ON DELETE |
| `locale` | VARCHAR(10) | No | None | Yes | No | None | Locale code (en, hi) |
| `name` | VARCHAR(150) | No | None | No | No | None | Localized category name |
| `description` | TEXT | Yes | NULL | No | No | None | Localized description |
| `seo_title` | VARCHAR(255) | Yes | NULL | No | No | None | Editorial SEO title |
| `seo_description` | VARCHAR(500) | Yes | NULL | No | No | None | Editorial SEO description |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |

*Constraint:* `UNIQUE(category_id, locale)`

---

## 7. Table: `genres`
*Purpose: Flat classification tags for books.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `slug` | VARCHAR(150) | No | None | Yes | Yes | None | Unique slug |
| `is_active` | TINYINT(1) | No | 1 | Yes | No | None | Active toggle |
| `sort_order` | SMALLINT UNSIGNED | No | 0 | No | No | None | Ordering |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `deleted_at` | TIMESTAMP | Yes | NULL | No | No | None | Soft delete timestamp |

---

## 8. Table: `genre_translations`
*Purpose: Localized genre names and summaries.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `genre_id` | BIGINT UNSIGNED | No | None | Yes | No | `genres.id` | CASCADE ON DELETE |
| `locale` | VARCHAR(10) | No | None | Yes | No | None | Locale code |
| `name` | VARCHAR(150) | No | None | No | No | None | Localized genre name |
| `description` | TEXT | Yes | NULL | No | No | None | Localized description |
| `seo_title` | VARCHAR(255) | Yes | NULL | No | No | None | Editorial SEO title |
| `seo_description` | VARCHAR(500) | Yes | NULL | No | No | None | Editorial SEO description |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |

*Constraint:* `UNIQUE(genre_id, locale)`

---

## 9. Table: `books`
*Purpose: Master book record, publication attributes, engagement counters, and legal foundation.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `slug` | VARCHAR(150) | No | None | Yes | Yes | None | Canonical URL slug |
| `status` | VARCHAR(20) | No | 'draft' | Yes | No | None | BookStatus backed enum |
| `publication_year` | SMALLINT UNSIGNED | Yes | NULL | Yes | No | None | Year first published |
| `publisher` | VARCHAR(150) | Yes | NULL | No | No | None | Original publisher name |
| `isbn` | VARCHAR(20) | Yes | NULL | Yes | No | None | Standard ISBN-10 |
| `isbn13` | VARCHAR(20) | Yes | NULL | Yes | No | None | Standard ISBN-13 |
| `pages` | SMALLINT UNSIGNED | Yes | NULL | No | No | None | Total page count |
| `edition` | VARCHAR(50) | Yes | NULL | No | No | None | Edition label |
| `original_language_id` | BIGINT UNSIGNED | Yes | NULL | Yes | No | `languages.id` | NULL ON DELETE |
| `rating_average` | DECIMAL(3, 2) | No | 0.00 | Yes | No | None | Denormalized rating cache |
| `rating_count` | INT UNSIGNED | No | 0 | No | No | None | Total ratings count |
| `review_count` | INT UNSIGNED | No | 0 | No | No | None | Total written reviews count |
| `view_count` | BIGINT UNSIGNED | No | 0 | No | No | None | Book detail page views |
| `source_url` | VARCHAR(500) | Yes | NULL | No | No | None | Archival / source URL |
| `license_type` | VARCHAR(100) | Yes | NULL | No | No | None | License label (Public Domain) |
| `rights_holder` | VARCHAR(255) | Yes | NULL | No | No | None | Legal copyright holder |
| `copyright_status` | VARCHAR(50) | Yes | NULL | No | No | None | Status string |
| `permission_notes` | TEXT | Yes | NULL | No | No | None | Legal audit notes |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `deleted_at` | TIMESTAMP | Yes | NULL | No | No | None | Soft delete timestamp |

---

## 10. Table: `book_translations`
*Purpose: Localized book titles, descriptions, themes, and SEO tags.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `book_id` | BIGINT UNSIGNED | No | None | Yes | No | `books.id` | CASCADE ON DELETE |
| `locale` | VARCHAR(10) | No | None | Yes | No | None | Locale (en, hi) |
| `title` | VARCHAR(255) | No | None | No | No | None | Localized book title |
| `short_description` | VARCHAR(500) | Yes | NULL | No | No | None | Brief teaser description |
| `description` | TEXT | Yes | NULL | No | No | None | Full synopsis |
| `summary` | TEXT | Yes | NULL | No | No | None | Chapter/literary summary |
| `key_themes` | JSON | Yes | NULL | No | No | None | Array of key literary themes |
| `why_read` | TEXT | Yes | NULL | No | No | None | Reader motivation text |
| `seo_title` | VARCHAR(255) | Yes | NULL | No | No | None | Localized meta title |
| `seo_description` | VARCHAR(500) | Yes | NULL | No | No | None | Localized meta description |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |

*Constraint:* `UNIQUE(book_id, locale)`

---

## 11. Table: `book_authors`
*Purpose: Many-to-Many pivot between books and authors with contributor role.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `book_id` | BIGINT UNSIGNED | No | None | Yes | No | `books.id` | CASCADE ON DELETE |
| `author_id` | BIGINT UNSIGNED | No | None | Yes | No | `authors.id` | CASCADE ON DELETE |
| `role` | VARCHAR(30) | No | 'author' | No | No | None | AuthorRole enum (author, translator) |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |

*Constraint:* `UNIQUE(book_id, author_id, role)`

---

## 12. Table: `book_categories`
*Purpose: Many-to-Many pivot between books and categories.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `book_id` | BIGINT UNSIGNED | No | None | Yes | No | `books.id` | CASCADE ON DELETE |
| `category_id` | BIGINT UNSIGNED | No | None | Yes | No | `categories.id` | CASCADE ON DELETE |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |

*Constraint:* `UNIQUE(book_id, category_id)`

---

## 13. Table: `book_genres`
*Purpose: Many-to-Many pivot between books and genres.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `book_id` | BIGINT UNSIGNED | No | None | Yes | No | `books.id` | CASCADE ON DELETE |
| `genre_id` | BIGINT UNSIGNED | No | None | Yes | No | `genres.id` | CASCADE ON DELETE |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |

*Constraint:* `UNIQUE(book_id, genre_id)`

---

## 14. Table: `book_languages`
*Purpose: Many-to-Many pivot modeling multi-language edition availability.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `book_id` | BIGINT UNSIGNED | No | None | Yes | No | `books.id` | CASCADE ON DELETE |
| `language_id` | BIGINT UNSIGNED | No | None | Yes | No | `languages.id` | CASCADE ON DELETE |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |

*Constraint:* `UNIQUE(book_id, language_id)`

---

## 15. Table: `reviews`
*Purpose: User ratings, reviews, and anti-abuse telemetry.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `book_id` | BIGINT UNSIGNED | No | None | Yes | No | `books.id` | CASCADE ON DELETE |
| `user_id` | BIGINT UNSIGNED | No | None | Yes | No | `users.id` | CASCADE ON DELETE |
| `rating` | TINYINT UNSIGNED | No | None | No | No | None | 1 to 5 integer |
| `title` | VARCHAR(150) | Yes | NULL | No | No | None | Review title |
| `body` | TEXT | Yes | NULL | No | No | None | Written review text |
| `status` | VARCHAR(20) | No | 'pending' | Yes | No | None | ReviewStatus enum |
| `is_verified` | TINYINT(1) | No | 0 | No | No | None | Verified reader badge |
| `ip_hash` | VARCHAR(64) | Yes | NULL | Yes | No | None | SHA-256 anonymized IP |
| `user_agent_hash` | VARCHAR(64) | Yes | NULL | No | No | None | SHA-256 hashed browser UA |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |

*Constraints & Indexes:*
- `UNIQUE(user_id, book_id)` (One review per user per book)
- `INDEX(book_id, status)` (Optimizes fetching approved reviews on book pages)

---

## 16. Table: `favorites`
*Purpose: Bookmarks and personal reading list additions.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `user_id` | BIGINT UNSIGNED | No | None | Yes | No | `users.id` | CASCADE ON DELETE |
| `book_id` | BIGINT UNSIGNED | No | None | Yes | No | `books.id` | CASCADE ON DELETE |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | No | No | None | Added timestamp |

*Constraints & Indexes:*
- `UNIQUE(user_id, book_id)`
- `INDEX(book_id)`

---

## 17. Table: `media`
*Purpose: Polymorphic media assets matching Spatie Media Library v11 official schema.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `model_type` | VARCHAR(255) | No | None | Yes | No | None | Polymorphic class name |
| `model_id` | BIGINT UNSIGNED | No | None | Yes | No | None | Polymorphic entity ID |
| `uuid` | CHAR(36) | Yes | NULL | Yes | Yes | None | Obfuscated public UUID |
| `collection_name` | VARCHAR(255) | No | None | No | No | None | 'covers', 'authors', 'documents' |
| `name` | VARCHAR(255) | No | None | No | No | None | File display name |
| `file_name` | VARCHAR(255) | No | None | No | No | None | Stored disk filename |
| `mime_type` | VARCHAR(255) | Yes | NULL | No | No | None | MIME type (image/webp) |
| `disk` | VARCHAR(255) | No | None | No | No | None | Storage disk ('public', 'private_pdfs') |
| `conversions_disk` | VARCHAR(255) | Yes | NULL | No | No | None | Conversions storage disk |
| `size` | BIGINT UNSIGNED | No | None | No | No | None | File size in bytes |
| `manipulations` | JSON | No | None | No | No | None | Transformation metadata |
| `custom_properties` | JSON | No | None | No | No | None | Custom attributes |
| `generated_conversions` | JSON | No | None | No | No | None | Conversion map |
| `responsive_images` | JSON | No | None | No | No | None | Responsive image map |
| `order_column` | INT UNSIGNED | Yes | NULL | Yes | No | None | Display sort position |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |

---

## 18. Table: `seo_meta`
*Purpose: Polymorphic technical crawler directives, canonical links, and social metadata.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `seoable_type` | VARCHAR(255) | No | None | Yes | No | None | Polymorphic entity class |
| `seoable_id` | BIGINT UNSIGNED | No | None | Yes | No | None | Polymorphic entity ID |
| `locale` | VARCHAR(10) | No | 'en' | Yes | No | None | Page language code |
| `canonical_url` | VARCHAR(500) | Yes | NULL | No | No | None | Canonical override URL |
| `robots` | VARCHAR(50) | No | 'index, follow' | No | No | None | Crawler robots directive |
| `og_image` | VARCHAR(500) | Yes | NULL | No | No | None | OpenGraph social preview image |
| `schema_type` | VARCHAR(50) | Yes | NULL | No | No | None | Schema.org type (Book, Person) |
| `meta_data` | JSON | Yes | NULL | No | No | None | Custom structured markup/tags |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |

*Constraint:* `UNIQUE(seoable_type, seoable_id, locale)`

---

## 19. Table: `redirects`
*Purpose: 301/302 URL redirection for legacy WordPress preservation and canonical migrations.*

| Column | Type | Nullable | Default | Index | Unique | Foreign Key | Notes |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | No | AUTO_INCREMENT | PK | Yes | None | Primary Key |
| `source_path` | VARCHAR(500) | No | None | Yes | Yes | None | Requested incoming path |
| `destination_path` | VARCHAR(500) | No | None | No | No | None | Target redirection destination |
| `status_code` | SMALLINT UNSIGNED | No | 301 | No | No | None | HTTP redirect code (301, 302, 307, 308) |
| `is_active` | TINYINT(1) | No | 1 | Yes | No | None | Redirection active flag |
| `hit_count` | INT UNSIGNED | No | 0 | No | No | None | Redirection usage telemetry |
| `last_accessed_at` | TIMESTAMP | Yes | NULL | No | No | None | Last redirected timestamp |
| `created_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
| `updated_at` | TIMESTAMP | Yes | NULL | No | No | None | Timestamp |
