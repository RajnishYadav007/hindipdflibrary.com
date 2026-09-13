# SEO Schema Architectural Decision

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Phase:** Phase 3 — Database & Core Data Model  
**Updated:** September 13, 2026  

---

## 1. Context & Architectural Challenge

Hindi PDF Library is an SEO-driven platform where organic search traffic is the primary acquisition channel. SEO metadata involves two distinct categories of information:
1. **Localized Editorial Content:** Meta title and meta description written specifically in English, Hindi, or regional languages to maximize click-through rates on search engine result pages (SERPs).
2. **Technical Crawler Directives & Social Previews:** Canonical URL overrides, robots index/noindex directives, OpenGraph preview images, Schema.org entity types, and custom structured JSON-LD markup.

If all SEO fields are placed solely in a generic `seo_meta` table, queries must join `seo_meta` multiple times per language, introducing latency. Conversely, if technical directives are placed in translation tables, duplicate robots and canonical rules proliferate across languages.

---

## 2. The Decision: Separation of Editorial vs Technical SEO

We adopted a **clean separation of concerns**:

### 2.1. Editorial SEO Content $\to$ Normalized Translation Tables
- Columns: `seo_title` (`VARCHAR(255)`), `seo_description` (`VARCHAR(500)`)
- Location: `book_translations`, `author_translations`, `category_translations`, `genre_translations`
- **Rationale:** The editorial meta title and description are strictly language-dependent. They belong naturally with the localized title, synopsis, and author biography. When a book page in Hindi (`/hi/book/godaan`) is rendered, eager loading `book.translation('hi')` retrieves both page body content and SERP meta tags in a single SQL query with zero extra joins.

### 2.2. Technical Crawler Directives $\to$ Polymorphic `seo_meta` Table
- Location: `seo_meta`
- Columns:
  - `seoable_type`, `seoable_id` (Polymorphic morphs)
  - `locale` (VARCHAR 10)
  - `canonical_url` (VARCHAR 500, nullable)
  - `robots` (VARCHAR 50, default 'index, follow')
  - `og_image` (VARCHAR 500, nullable)
  - `schema_type` (VARCHAR 50, e.g. 'Book', 'Person')
  - `meta_data` (JSON, for custom microdata/tags)
- Constraint: `UNIQUE(seoable_type, seoable_id, locale)`
- **Rationale:** Allows any model (or future static landing page) to attach explicit crawler directives, custom social share images, or structured data overrides on a per-locale basis without polluting core entity tables.

---

## 3. Consequences & Benefits
- **Zero Duplicate Sources of Truth:** Title and description are authored once in the translation tables.
- **Minimal SQL Overhead:** Public frontend views retrieve complete localized metadata in the primary translation query.
- **Maximum Extensibility:** Future models (e.g. Collections, Publisher pages) can adopt `seoMeta()` instantly via the polymorphic relationship.
