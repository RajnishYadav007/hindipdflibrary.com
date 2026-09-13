# Phase 8 — Architectural & Technical Decisions Log

## 1. Decision: Unified Ratings and Reviews Persistence
- **Context**: Some applications separate `ratings` and `reviews` into two tables, creating double-entry synchronization headaches and N+1 join queries.
- **Decision**: Persisted ratings directly inside the `reviews` table (`rating` column, 1–5). A rating without written text simply leaves `title` and `body` as `null`.
- **Consequence**: Single database query, guaranteed 1:1 parity between ratings and written reflections, and clean `UNIQUE(user_id, book_id)` integrity.

## 2. Decision: Recalculation Action & Model Events
- **Context**: Cached book metrics (`rating_average`, `rating_count`, `review_count`) can become desynchronized if updates occur via different entry points (Filament admin, public Livewire, console commands, database seeders).
- **Decision**: Created `RecalculateBookMetricsAction` and registered model hooks (`saved`, `deleted`) on the `Review` Eloquent model.
- **Consequence**: Any review change automatically updates the parent book with zero code duplication, while public read operations enjoy instant query performance without expensive `AVG()` calculations.

## 3. Decision: Single-Query Rating Distribution Calculation
- **Context**: Displaying 5★ to 1★ breakdown bars typically risks running five separate `COUNT(*)` queries on the book detail page.
- **Decision**: Executed a single grouped aggregate query in `BookController`:
  ```php
  $book->reviews()->approved()->selectRaw('rating, count(*) as count')->groupBy('rating')->pluck('count', 'rating')->toArray();
  ```
- **Consequence**: Exactly one optimized database query to power the entire 5-bar distribution graph.
