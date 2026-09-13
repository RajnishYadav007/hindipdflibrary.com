# Phase 6 — Search & Faceted Catalog Specification (`/search`)

## 1. Overview
The search page (`resources/views/public/search/index.blade.php`) offers full catalog discovery through keyword search and multi-faceted filtering.

## 2. Search Capabilities
- **Keywords Query (`q`)**:
  - Matches across book titles (`book_translations.title`).
  - Matches across author names (`author_translations.name`).
  - Matches exact or partial ISBN (`isbn`, `isbn13`).
- **Faceted Filters**:
  - `category`: Filter books by specific category ID.
  - `genre`: Filter books by specific literary genre ID.
  - `language`: Filter books by language ID (matching both available translation languages and original language).
- **Sort Options (`sort`)**:
  - `newest`: Order by latest created.
  - `popular`: Order by `view_count` descending.
  - `rating`: Order by `rating_average` and `rating_count` descending.
  - `title`: Order by title slug ascending.
  - `oldest`: Order by earliest created.
- **Dynamic Filter Clearing**:
  - "Clear all filters & search" button resets the catalog when any filter or query is active.
- **Empty State**:
  - Friendly empty state with suggestions when no books match the specified criteria.
