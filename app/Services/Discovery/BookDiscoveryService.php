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

    /**
     * Infer the divine entity / deity from book attributes if deity is not explicitly set.
     */
    public function inferDeity(Book $book): ?string
    {
        if (! empty($book->deity)) {
            return $book->deity;
        }

        $haystack = strtolower($book->slug.' '.($book->title ?? ''));

        $keywordMap = [
            'hanuman' => ['hanuman', 'bajrang', 'maruti', 'sundarkand', 'sundar-kand', 'संकटमोचन', 'हनुमान', 'बजरंग'],
            'shiva' => ['shiv', 'shiva', 'mahadev', 'bholenath', 'rudra', 'शिव', 'महादेव', 'भोलेनाथ', 'रुद्र'],
            'ram' => ['ram', 'rama', 'ramayan', 'ramayana', 'ramcharitmanas', 'राम', 'रामायण', 'रामचरितमानस'],
            'krishna' => ['krishna', 'gita', 'geeta', 'bhagavad', 'govind', 'कृष्ण', 'गीता', 'भागवत'],
            'ganesh' => ['ganesh', 'ganpati', 'vinayak', 'गणेश', 'गणपति'],
            'durga' => ['durga', 'kali', 'ambey', 'amritwani', 'दुर्गा', 'काली', 'अंबे'],
            'lakshmi' => ['lakshmi', 'laxmi', 'महालक्ष्मी', 'लक्ष्मी'],
            'saraswati' => ['saraswati', 'सरस्वती'],
            'vishnu' => ['vishnu', 'narayan', 'विष्णु', 'नारायण'],
            'surya' => ['surya', 'aditya', 'सूर्य', 'आदित्य'],
            'vedas' => ['ved', 'puran', 'upanishad', 'वेद', 'पुराण', 'उपनिषद'],
        ];

        foreach ($keywordMap as $deityKey => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($haystack, $kw)) {
                    return $deityKey;
                }
            }
        }

        return null;
    }

    /**
     * Discover devotional prayers (Chalisas, Aartis, Stotras) related to a book or prayer's deity.
     */
    public function relatedPrayers(Book $book, int $limit = 6): Collection
    {
        $deity = $this->inferDeity($book);

        if (! $deity) {
            return new Collection;
        }

        return Book::published()
            ->where('id', '!=', $book->id)
            ->where(function ($query) use ($deity) {
                $query->where('deity', $deity)
                    ->orWhereIn('content_type', ['chalisa', 'aarti', 'stotra', 'mantra'])
                    ->where(function ($sub) use ($deity) {
                        $sub->where('slug', 'like', "%{$deity}%")
                            ->orWhereHas('translations', fn ($t) => $t->where('title', 'like', "%{$deity}%"));
                    });
            })
            ->where(function ($query) {
                $query->whereIn('content_type', ['chalisa', 'aarti', 'stotra', 'mantra'])
                    ->orWhereHas('translations', fn ($t) => $t->whereNotNull('lyrics')->where('lyrics', '!=', ''));
            })
            ->with($this->eagerLoadRelations)
            ->take($limit)
            ->get();
    }

    /**
     * Discover holy books and scriptures related to a prayer or book's deity.
     */
    public function relatedBooksForDeity(Book $book, int $limit = 6): Collection
    {
        $deity = $this->inferDeity($book);

        if (! $deity) {
            return new Collection;
        }

        return Book::published()
            ->where('id', '!=', $book->id)
            ->where(function ($query) use ($deity) {
                $query->where('deity', $deity)
                    ->orWhere(function ($sub) use ($deity) {
                        $sub->where('slug', 'like', "%{$deity}%")
                            ->orWhereHas('translations', fn ($t) => $t->where('title', 'like', "%{$deity}%"));
                    });
            })
            ->where(function ($query) {
                $query->where('content_type', 'book')
                    ->orWhereNull('content_type');
            })
            ->with($this->eagerLoadRelations)
            ->take($limit)
            ->get();
    }
}
