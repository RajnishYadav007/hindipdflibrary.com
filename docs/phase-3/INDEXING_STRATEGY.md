# Database Indexing Strategy

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Phase:** Phase 3 — Database & Core Data Model  
**Updated:** September 13, 2026  

---

## 1. Principles of Indexing

1. **Query-Driven Indexes:** Every index in this database exists solely to support verified query patterns in library discovery, filtering, and authorization.
2. **Zero Redundancy Rule:** We verify that unique constraints (which internally create primary B-tree indexes) are not duplicated with standalone single-column indexes.
3. **Compound Key Ordering:** Multi-column indexes place the most selective equality columns first, followed by range or sort columns.

---

## 2. Comprehensive Index Analysis

| Table | Index Columns | Type | Supported Query Pattern |
| :--- | :--- | :--- | :--- |
| `languages` | `code` | Unique | Route locale resolution (`/hi/...`, `/en/...`) |
| `languages` | `is_active` | Index | Language switcher filtering active options |
| `authors` | `slug` | Unique | Author landing pages (`/author/{slug}`) |
| `authors` | `is_active` | Index | Filtering active authors |
| `author_translations` | `(author_id, locale)` | Unique | Eager-loading translation for active locale |
| `author_translations` | `locale` | Index | Filtering translations across locale |
| `categories` | `slug` | Unique | Category detail views (`/category/{slug}`) |
| `categories` | `parent_id` | Index | Tree traversal: fetching top-level or subcategories |
| `categories` | `is_active` | Index | Filtering published categories |
| `category_translations` | `(category_id, locale)`| Unique | Eager-loading localized category titles |
| `genres` | `slug` | Unique | Genre page lookups (`/genre/{slug}`) |
| `genres` | `is_active` | Index | Active genre filtering |
| `genre_translations` | `(genre_id, locale)` | Unique | Localized genre title resolution |
| `books` | `slug` | Unique | Canonical book detail resolution (`/book/{slug}`) |
| `books` | `status` | Index | Filtering published books (`WHERE status = 'published'`) |
| `books` | `publication_year` | Index | Decade / year filter facets on catalog grids |
| `books` | `isbn` | Index | ISBN exact lookups |
| `books` | `isbn13` | Index | ISBN-13 exact lookups |
| `books` | `original_language_id` | Index | Filtering by original manuscript language |
| `books` | `rating_average` | Index | Top-rated book sorting (`ORDER BY rating_average DESC`) |
| `book_translations` | `(book_id, locale)` | Unique | Primary book title/synopsis hydration per locale |
| `book_translations` | `locale` | Index | Locale scoped searches |
| `book_authors` | `(book_id, author_id, role)`| Unique | Prevents duplicate author assignments |
| `book_authors` | `author_id` | Index | Fetching all books written by an author |
| `book_categories` | `(book_id, category_id)` | Unique | Prevents duplicate category assignments |
| `book_categories` | `category_id` | Index | Filtering books by category |
| `book_genres` | `(book_id, genre_id)` | Unique | Prevents duplicate genre tags |
| `book_genres` | `genre_id` | Index | Filtering books by genre |
| `book_languages` | `(book_id, language_id)` | Unique | Prevents duplicate language bindings |
| `book_languages` | `language_id` | Index | Filtering books available in Hindi or English |
| `reviews` | `(user_id, book_id)` | Unique | Prevents a user from spamming multiple reviews |
| `reviews` | `(book_id, status)` | Composite | Fetching approved reviews on book page |
| `reviews` | `ip_hash` | Index | Anti-abuse rate limiting and review velocity checks |
| `favorites` | `(user_id, book_id)` | Unique | Enforces single bookmark per user |
| `favorites` | `book_id` | Index | Calculating total favorites and popular bookmarks |
| `media` | `(model_type, model_id)`| Composite | Polymorphic entity asset hydration |
| `media` | `uuid` | Unique | Public secure media URLs |
| `media` | `order_column` | Index | Gallery and thumbnail display sorting |
| `seo_meta` | `(seoable_type, seoable_id, locale)` | Unique | Exact SEO metadata lookup per entity and locale |
| `redirects` | `source_path` | Unique | Instant lookup for incoming legacy/WordPress URLs |
| `redirects` | `is_active` | Index | Bypassing disabled redirects in middleware |
