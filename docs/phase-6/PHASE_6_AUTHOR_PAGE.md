# Phase 6 — Author Page Specification (`/authors/{slug}`)

## 1. Overview
The author profile page (`resources/views/public/authors/show.blade.php`) honors literary authors, poets, and scholars.

## 2. Layout & Features
1. **Header Profile Card**:
   - Circular portrait photo or elegant dual-character Devanagari/Latin initial badge.
   - Author name in active locale.
   - Published titles count badge.
   - Full biographical essay with typography styling.
2. **Catalog Grid**:
   - Responsive grid of all books authored by this individual using `<x-book-card />`.
   - Strictly scoped to `published` books (`$author->books()->published()`).
   - Standard 12-item pagination with query string preservation.
3. **Related Literary Figures**:
   - Curated list of other prominent active authors with published books count.
4. **Error Handling**:
   - Returns HTTP 404 if the author record has `is_active = false` or does not exist.
