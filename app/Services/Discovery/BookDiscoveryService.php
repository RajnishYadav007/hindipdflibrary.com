<?php

namespace App\Services\Discovery;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

class BookDiscoveryService
{
    /**
     * Standard eager-loaded relationships for cards and listings.
     *
     * @var array<int, string>
     */
    protected array $eagerLoadRelations = [
        'translations',
        'authors.translations',
        'categories.translations',
        'media',
    ];

    /**
     * Find published books by the same authors, excluding the specified book.
     */
    public function relatedByAuthor(Book $book, int $limit = 4): Collection
    {
        $authorIds = $book->relationLoaded('authors')
            ? $book->authors->pluck('id')
            : $book->authors()->pluck('authors.id');

        if ($authorIds->isEmpty()) {
            return new Collection;
        }

        return Book::published()
            ->where('id', '!=', $book->id)
            ->whereHas('authors', fn ($q) => $q->whereIn('authors.id', $authorIds))
            ->with($this->eagerLoadRelations)
            ->orderByDesc('rating_average')
            ->take($limit)
            ->get();
    }

    /**
     * Find published books in the same categories, excluding the specified book and previous exclusions.
     *
     * @param  Collection<int, Book>|array<int, int>|null  $exclude
     */
    public function relatedByCategory(Book $book, int $limit = 4, $exclude = null): Collection
    {
        $categoryIds = $book->relationLoaded('categories')
            ? $book->categories->pluck('id')
            : $book->categories()->pluck('categories.id');

        if ($categoryIds->isEmpty()) {
            return new Collection;
        }

        $excludeIds = collect([$book->id]);
        if ($exclude instanceof Collection) {
            $excludeIds = $excludeIds->merge($exclude->pluck('id'));
        } elseif (is_array($exclude)) {
            $excludeIds = $excludeIds->merge($exclude);
        }

        return Book::published()
            ->whereNotIn('id', $excludeIds->unique())
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->with($this->eagerLoadRelations)
            ->orderByDesc('rating_average')
            ->take($limit)
            ->get();
    }

    /**
     * Find published books in the same genres, excluding the specified book and previous exclusions.
     *
     * @param  Collection<int, Book>|array<int, int>|null  $exclude
     */
    public function relatedByGenre(Book $book, int $limit = 4, $exclude = null): Collection
    {
        $genreIds = $book->relationLoaded('genres')
            ? $book->genres->pluck('id')
            : $book->genres()->pluck('genres.id');

        if ($genreIds->isEmpty()) {
            return new Collection;
        }

        $excludeIds = collect([$book->id]);
        if ($exclude instanceof Collection) {
            $excludeIds = $excludeIds->merge($exclude->pluck('id'));
        } elseif (is_array($exclude)) {
            $excludeIds = $excludeIds->merge($exclude);
        }

        return Book::published()
            ->whereNotIn('id', $excludeIds->unique())
            ->whereHas('genres', fn ($q) => $q->whereIn('genres.id', $genreIds))
            ->with($this->eagerLoadRelations)
            ->orderByDesc('rating_average')
            ->take($limit)
            ->get();
    }

    /**
     * Discover popular published books by view count.
     */
    public function popularBooks(int $limit = 8): Collection
    {
        return Book::published()
            ->with($this->eagerLoadRelations)
            ->orderByDesc('view_count')
            ->take($limit)
            ->get();
    }

    /**
     * Discover highly-rated featured published books.
     */
    public function featuredBooks(int $limit = 8): Collection
    {
        return Book::published()
            ->with($this->eagerLoadRelations)
            ->orderByDesc('rating_average')
            ->orderByDesc('rating_count')
            ->take($limit)
            ->get();
    }

    /**
     * Discover recently published books.
     */
    public function recentBooks(int $limit = 8): Collection
    {
        return Book::published()
            ->with($this->eagerLoadRelations)
            ->latest('id')
            ->take($limit)
            ->get();
    }
}
