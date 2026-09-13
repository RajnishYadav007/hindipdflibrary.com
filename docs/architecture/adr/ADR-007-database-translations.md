# ADR-007: Database-Driven Multilingual Architecture

**Date:** September 13, 2026  
**Status:** Accepted  
**Deciders:** Lead Solutions Architect, Database Architect, SEO Architect  

---

## Context
Hindi PDF Library is an English-first public platform with Hindi as an essential second language, and future expansion to additional regional Indian languages (e.g., Sanskrit, Bengali, Marathi). We must model dynamic content (book titles, author bios, category descriptions) and static UI labels (buttons, navigation, footers) for multilingual access and SEO indexability.

## Decision
Adopt a **hybrid multilingual architecture**:
1. **Static UI Strings:** Standard Laravel PHP language files (`lang/en/...`, `lang/hi/...`).
2. **Dynamic Entity Content:** Database-level JSON column translation using `spatie/laravel-translatable` on MySQL 8 `utf8mb4` tables.
3. **URL Routing:** Explicit locale prefixing (`/en/...`, `/hi/...`) with localized slug support and `<link rel="alternate" hreflang="...">` headers.

## Reason
1. **Performance & Simplicity:** Storing translations in native MySQL JSON columns (`{"en": "Godan", "hi": "गोदान"}`) eliminates the need for 10+ relational join tables (`book_translations`, `author_translations`), drastically reducing query complexity on shared MySQL.
2. **SEO Dominance:** Dedicated URLs per language (`/en/books/godan` and `/hi/books/godan`) allow Google to index both language versions independently with zero duplicate content penalties.
3. **Admin Ergonomics:** Filament provides first-class field translation tabs for `spatie/laravel-translatable` models out of the box.

## Alternatives Considered
- **Relational Translation Tables (Astrotomic / Doctrine model):** Separate tables for every entity's translations. Highly normalized, but creates heavy SQL join overhead and verbose Eloquent queries.
- **Single Monolingual Table with Duplicated Rows:** Highly error-prone; breaks relationships (reviews and downloads would have to link across multiple duplicated book records).

## Consequences
- Requires MySQL 8.0+ JSON support (standard on modern hosts).
- Columns storing translated data are typed as `JSON` in migrations.

## Future Migration Path
Additional languages (e.g., `mr` for Marathi, `sa` for Sanskrit) can be added simply by registering the locale in `config/app.php` and adding language tabs in Filament.
