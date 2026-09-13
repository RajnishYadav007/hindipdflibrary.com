# Workstream I: Search Strategy & Multilingual Retrieval

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Component:** Search Engine Architecture  
**Phases:** V1 (MySQL Native) ➔ Future (Meilisearch Engine)  
**Auditor:** Lead Solutions Architect & Senior Database Architect  

---

## 1. Executive Summary

Search is the primary discovery mechanism for a digital library. The search architecture must support:
1. English titles and authors (e.g., *"Munshi Premchand"*, *"Godan"*).
2. Hindi Devanagari script (e.g., *"मुंशी प्रेमचंद"*, *"गोदान"*).
3. Transliterated / Phonetic queries (e.g., *"Godaan"*, *"Kafan"*).
4. Specific taxonomy filters (Category, Genre, Language, Year, ISBN).

In V1, **MySQL 8.0+ FULLTEXT indexing** will be leveraged natively without introducing external search daemons like Meilisearch or Elasticsearch.

---

## 2. V1: MySQL Native Search Architecture

### 2.1. Schema & Index Design
MySQL InnoDB supports `FULLTEXT` indexes on `VARCHAR` and `TEXT` columns with `utf8mb4` encoding.

```sql
-- Migration snippet for books table
CREATE TABLE books (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    title_transliterated VARCHAR(255) NULL, -- Phonetic/English spelling for Hindi titles
    slug VARCHAR(255) UNIQUE NOT NULL,
    author_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    isbn VARCHAR(32) NULL INDEX,
    description TEXT NULL,
    language_code VARCHAR(10) DEFAULT 'hi' INDEX,
    published_year SMALLINT UNSIGNED NULL INDEX,
    view_count INT UNSIGNED DEFAULT 0 INDEX,
    download_count INT UNSIGNED DEFAULT 0 INDEX,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    -- Composite FULLTEXT index for high-speed multi-column discovery
    FULLTEXT INDEX idx_books_search (title, title_transliterated, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.2. Query Strategy & Fallback Cascade
To provide accurate results without complex microservices:

```php
// Search Service Logic
public function search(string $term, array $filters = []): LengthAwarePaginator
{
    $cleanTerm = trim($term);
    
    $query = Book::query()
        ->with(['author', 'category', 'genres'])
        ->when($cleanTerm, function ($q) use ($cleanTerm) {
            // Priority 1: Exact ISBN match
            if (preg_match('/^[0-9\-]{10,17}$/', $cleanTerm)) {
                return $q->where('isbn', $cleanTerm);
            }

            // Priority 2: MySQL FULLTEXT Boolean Search
            $booleanTerm = '+' . implode('* +', explode(' ', $cleanTerm)) . '*';
            
            return $q->whereRaw(
                "MATCH(title, title_transliterated, description) AGAINST(? IN BOOLEAN MODE)",
                [$booleanTerm]
            )->orWhereHas('author', function ($authorQuery) use ($cleanTerm) {
                $authorQuery->where('name', 'LIKE', "%{$cleanTerm}%")
                            ->orWhere('name_transliterated', 'LIKE', "%{$cleanTerm}%");
            });
        });

    // Apply Taxonomy Filters (O(1) B-tree index lookups)
    if (!empty($filters['category_id'])) {
        $query->where('category_id', $filters['category_id']);
    }
    if (!empty($filters['genre_id'])) {
        $query->whereHas('genres', fn($g) => $g->where('genres.id', $filters['genre_id']));
    }
    if (!empty($filters['language'])) {
        $query->where('language_code', $filters['language']);
    }

    return $query->orderByDesc('view_count')->paginate(18);
}
```

---

## 3. Multilingual & Devanagari Search Considerations

1. **Character Set & Collation:**
   The database collation must be `utf8mb4_unicode_ci` or `utf8mb4_0900_ai_ci`. This guarantees accent/case-insensitive comparison and accurate sorting of Devanagari codepoints (`U+0900` to `U+097F`).
2. **The Transliteration Challenge:**
   Indian users often search for Hindi books using the Latin alphabet (e.g., typing *"Premchand ki kahaniya"* instead of *"प्रेमचंद की कहानियां"*).
   - **Architecture Solution:** Maintain a `title_transliterated` column and `author_transliterated` column populated automatically during book creation.
   - This enables matching regardless of whether the user inputs Devanagari or English script.
3. **Stop-Words & N-Gram Parsing:**
   Standard MySQL full-text parsing splits words on spaces, which works effectively for both Hindi and English. If sub-word matching is needed, MySQL InnoDB's ngram full-text parser can be activated without third-party tools.

---

## 4. Livewire Reactive Search Integration

The public search interface will use a Livewire 4 component (`App\Livewire\BookSearch`):
- **Debounced Input:** `wire:model.live.debounce.300ms="search"` ensures the database is not spammed on every keystroke.
- **Deep-Linking via Query String:** `#[Url]` attribute keeps the browser URL updated (e.g., `https://hindipdflibrary.com/books?q=premchand&cat=fiction`), ensuring users can share and bookmark search results and search engines can crawl taxonomy combinations.

---

## 5. Future Meilisearch Upgrade Path

When the library catalog exceeds 25,000 titles or traffic justifies a dedicated search server:
1. Add `laravel/scout` and `meilisearch/meilisearch-php`.
2. Configure Scout in `config/scout.php`:
   ```php
   'driver' => env('SCOUT_DRIVER', 'database'), // Defaults to database in V1
   ```
3. In Phase 2+, switching `SCOUT_DRIVER=meilisearch` in `.env` and running `php artisan scout:import "App\Models\Book"` activates typo-tolerant, instant search with zero architectural refactoring of models.
