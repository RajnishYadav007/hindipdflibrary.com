# Phase 10 — Search & Discovery Engine: Architecture

## 1. Layer Responsibilities & Data Flow

```
                               HTTP GET /{locale}/search?q=...
                                              │
                                              ▼
                                Presentation Layer
                 ┌────────────────────────────────────────────────────────┐
                 │ App\Http\Controllers\SearchController                  │
                 │ resources/views/public/search/index.blade.php          │
                 │   - Faceted dropdown filters (Category, Author, etc.)  │
                 │   - Query term highlight & empty state fallback        │
                 └────────────────────────────┬───────────────────────────┘
                                              │
                                              ▼
                                 Application Layer
                 ┌────────────────────────────────────────────────────────┐
                 │ App\Contracts\Search\SearchEngineInterface             │
                 │               ▲                                        │
                 │               │ (Bound in AppServiceProvider)          │
                 │               │                                        │
                 │ App\Services\Search\MySqlSearchEngine                  │
                 │   - Query tokenization & sanitization                  │
                 │   - Relevance scoring algorithm                        │
                 │   - Facet aggregation & filtering                      │
                 │                                                        │
                 │ App\Services\Discovery\BookDiscoveryService            │
                 │   - Related by Author, Category, Genre                 │
                 │   - Fallback recommendations for 0-result queries      │
                 └────────────────────────────┬───────────────────────────┘
                                              │
                                              ▼
                              Domain / Persistence Layer
                 ┌────────────────────────────────────────────────────────┐
                 │ App\Models\Book, Author, Category, Genre               │
                 │ Migration: 2026_09_13_200001_add_search_indexes.php    │
                 │ Composite Indexes: [status, published_at, title],      │
                 │                    [isbn], [isbn13]                    │
                 └────────────────────────────────────────────────────────┘
```

## 2. Search Engine Interface (`SearchEngineInterface`)
Defines contract methods:
```php
interface SearchEngineInterface
{
    public function search(string $query, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function count(string $query, array $filters = []): int;
}
```

## 3. Relevance Scoring Algorithm
When searching for query `$term`, the MySQL engine applies parameterized score weights:
- `100` if `books.title = :exact`
- `80` if `books.title LIKE :prefix`
- `60` if `books.title LIKE :contains`
- `50` if `books.isbn = :exact OR books.isbn13 = :exact`
- `40` if `authors.name LIKE :contains`
- `20` if `categories.name LIKE :contains OR genres.name LIKE :contains`
- Total score = sum of matching clause weights.
- Filter: books with score > 0 or matching facets.
- Order by `relevance_score DESC, rating_average DESC, downloads_count DESC, created_at DESC`.
