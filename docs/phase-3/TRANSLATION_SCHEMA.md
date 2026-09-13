# Multilingual Translation Schema Architecture

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Phase:** Phase 3 — Database & Core Data Model  
**Updated:** September 13, 2026  

---

## 1. Architectural Evolution: ADR-007 vs Phase 3 Requirement

In Phase 0, `ADR-007` initially recommended storing translations inside native MySQL JSON columns using `spatie/laravel-translatable`. 

However, following comprehensive audit of digital library query workloads, full-text search requirements, and multi-language publishing, Phase 3 mandated a **fully normalized relational translation architecture**:
- `book_translations`
- `author_translations`
- `category_translations`
- `genre_translations`

### Why Relational Translations Are Superior for a Digital Library:
1. **Full-Text Natural Language Search:** MySQL 8 `FULLTEXT` indexing works natively on Devanagari text in standard `VARCHAR`/`TEXT` columns. MySQL cannot perform efficient full-text natural language queries inside dynamic JSON object paths on shared hosting.
2. **Strict Uniqueness:** Enforces `UNIQUE(entity_id, locale)` at the database engine level, preventing corrupt duplicate translations.
3. **Database-Level Sorting & Collation:** Allows sorting by localized book title (`ORDER BY book_translations.title ASC`) directly in SQL using `utf8mb4_unicode_ci`.
4. **Clean Decoupling:** Non-translatable operational fields (slug, status, isbn, publication year, review counters, ratings) are completely isolated from translatable editorial content.

---

## 2. Table Specifications & Schema Structure

```
books                                  book_translations
├── id (PK)                            ├── id (PK)
├── slug                               ├── book_id (FK -> books.id, CASCADE)
├── status                             ├── locale (VARCHAR 10)
├── publication_year                   ├── title (VARCHAR 255)
├── isbn                               ├── short_description (VARCHAR 500)
├── pages                              ├── description (TEXT)
├── rating_average                     ├── summary (TEXT)
└── ...                                ├── key_themes (JSON array)
                                       ├── why_read (TEXT)
                                       ├── seo_title (VARCHAR 255)
                                       └── seo_description (VARCHAR 500)
                                       └── UNIQUE(book_id, locale)
```

---

## 3. Eloquent Integration & Trait Architecture

To avoid code duplication across models while preserving native Eloquent conventions, the [`HasTranslations`](file:///d:/Full%20Stack/hindipdflibrary.com/app/Models/Concerns/HasTranslations.php) trait was implemented in `app/Models/Concerns/HasTranslations.php`:

```php
// Fetch all translations
$book->translations;

// Fetch translation for current application locale (e.g. 'hi' or 'en')
$book->translation();

// Fetch translation for a specific locale
$book->translation('hi')->first();
```

---

## 4. Supported Locales & Expansion

The schema supports any standard ISO 639-1 or BCP 47 locale code (e.g., `hi`, `en`, `sa`, `mr`, `bn`, `ur`, `te`, `ta`). Additional languages can be introduced dynamically by inserting a record into `languages` and adding translated rows to the translation tables without modifying table schema.
