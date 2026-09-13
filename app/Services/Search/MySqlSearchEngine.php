<?php

namespace App\Services\Search;

use App\Contracts\Search\SearchEngineInterface;
use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class MySqlSearchEngine implements SearchEngineInterface
{
    /**
     * Search published books catalogue using MySQL queries with deterministic relevance ranking.
     *
     * @param  array<string, mixed>  $filters
     */
    public function search(string $query, string $locale, array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        // 1. Sanitize & Normalize Query
        $cleanQuery = trim(preg_replace('/\s+/', ' ', $query) ?? '');
        $cleanQuery = Str::limit($cleanQuery, 100, '');

        $booksQuery = Book::published()
            ->with(['translations', 'authors.translations', 'categories.translations', 'media']);

        // 2. Full-catalogue or keyword search
        if ($cleanQuery !== '') {
            $this->applyKeywordSearch($booksQuery, $cleanQuery, $locale);
            $this->applyRelevanceRanking($booksQuery, $cleanQuery);
        }

        // 3. Faceted Filters
        $this->applyFilters($booksQuery, $filters);

        // 4. Fallback or User-selected Sorting
        $sort = $filters['sort'] ?? ($cleanQuery === '' ? 'newest' : null);
        if ($sort) {
            match ($sort) {
                'popular' => $booksQuery->orderByDesc('view_count'),
                'rating' => $booksQuery->orderByDesc('rating_average')->orderByDesc('rating_count'),
                'title' => $booksQuery->orderBy('slug'),
                'oldest' => $booksQuery->oldest('id'),
                default => $booksQuery->latest('id'),
            };
        } elseif ($cleanQuery !== '') {
            // Secondary tie-breakers after relevance_score
            $booksQuery->orderByDesc('rating_average')
                ->orderByDesc('view_count')
                ->latest('id');
        } else {
            $booksQuery->latest('id');
        }

        return $booksQuery->paginate($perPage);
    }

    /**
     * Apply keyword search conditions across searchable entities.
     */
    protected function applyKeywordSearch(Builder $query, string $searchTerm, string $locale): void
    {
        $escaped = str_replace(['%', '_'], ['\%', '\_'], $searchTerm);
        $likePattern = "%{$escaped}%";

        $query->where(function (Builder $q) use ($searchTerm, $likePattern) {
            // 1. Book translations (title, short_description, key_themes)
            $q->whereHas('translations', function (Builder $sub) use ($likePattern, $searchTerm) {
                $sub->where('title', 'like', $likePattern)
                    ->orWhere('short_description', 'like', $likePattern)
                    ->orWhereJsonContains('key_themes', $searchTerm);
            })
            // 2. Author translations
                ->orWhereHas('authors.translations', function (Builder $sub) use ($likePattern) {
                    $sub->where('name', 'like', $likePattern);
                })
            // 3. Category translations
                ->orWhereHas('categories.translations', function (Builder $sub) use ($likePattern) {
                    $sub->where('name', 'like', $likePattern);
                })
            // 4. Genre translations
                ->orWhereHas('genres.translations', function (Builder $sub) use ($likePattern) {
                    $sub->where('name', 'like', $likePattern);
                })
            // 5. ISBN / ISBN13
                ->orWhere('isbn', $searchTerm)
                ->orWhere('isbn13', $searchTerm);
        });
    }

    /**
     * Calculate deterministic relevance ranking using parameterized SQL expressions.
     */
    protected function applyRelevanceRanking(Builder $query, string $searchTerm): void
    {
        $escaped = str_replace(['%', '_'], ['\%', '\_'], $searchTerm);
        $prefixPattern = "{$escaped}%";
        $containsPattern = "%{$escaped}%";

        $query->select('books.*');

        $query->selectRaw('
            (
                (CASE WHEN EXISTS (
                    SELECT 1 FROM book_translations bt 
                    WHERE bt.book_id = books.id AND bt.title = ?
                ) THEN 100 ELSE 0 END) +
                (CASE WHEN EXISTS (
                    SELECT 1 FROM book_translations bt 
                    WHERE bt.book_id = books.id AND bt.title LIKE ?
                ) THEN 80 ELSE 0 END) +
                (CASE WHEN EXISTS (
                    SELECT 1 FROM book_translations bt 
                    WHERE bt.book_id = books.id AND bt.title LIKE ?
                ) THEN 60 ELSE 0 END) +
                (CASE WHEN books.isbn = ? OR books.isbn13 = ? THEN 50 ELSE 0 END) +
                (CASE WHEN EXISTS (
                    SELECT 1 FROM book_authors ba 
                    JOIN author_translations at ON at.author_id = ba.author_id 
                    WHERE ba.book_id = books.id AND at.name LIKE ?
                ) THEN 40 ELSE 0 END) +
                (CASE WHEN EXISTS (
                    SELECT 1 FROM book_categories bc 
                    JOIN category_translations ct ON ct.category_id = bc.category_id 
                    WHERE bc.book_id = books.id AND ct.name LIKE ?
                ) THEN 20 ELSE 0 END) +
                (CASE WHEN EXISTS (
                    SELECT 1 FROM book_genres bg 
                    JOIN genre_translations gt ON gt.genre_id = bg.genre_id 
                    WHERE bg.book_id = books.id AND gt.name LIKE ?
                ) THEN 20 ELSE 0 END)
            ) as relevance_score
        ', [
            $searchTerm,
            $prefixPattern,
            $containsPattern,
            $searchTerm,
            $searchTerm,
            $containsPattern,
            $containsPattern,
            $containsPattern,
        ]);

        $query->orderByDesc('relevance_score');
    }

    /**
     * Apply active faceted filters to the search query.
     *
     * @param  array<string, mixed>  $filters
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['category'])) {
            $categoryId = (int) $filters['category'];
            $query->whereHas('categories', fn (Builder $q) => $q->where('categories.id', $categoryId));
        }

        if (! empty($filters['genre'])) {
            $genreId = (int) $filters['genre'];
            $query->whereHas('genres', fn (Builder $q) => $q->where('genres.id', $genreId));
        }

        if (! empty($filters['author'])) {
            $authorId = (int) $filters['author'];
            $query->whereHas('authors', fn (Builder $q) => $q->where('authors.id', $authorId));
        }

        if (! empty($filters['language'])) {
            $languageId = (int) $filters['language'];
            $query->where(function (Builder $q) use ($languageId) {
                $q->whereHas('languages', fn (Builder $sub) => $sub->where('languages.id', $languageId))
                    ->orWhere('original_language_id', $languageId);
            });
        }
    }
}
