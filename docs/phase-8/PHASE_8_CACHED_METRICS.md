# Phase 8 — Cached Book Metrics & Performance Architecture

## 1. Denormalized Columns on `books`
- **`rating_average`**: Decimal (3, 2), e.g. `4.85`.
- **`rating_count`**: Total number of `approved` reviews for the book.
- **`review_count`**: Total number of `approved` reviews for the book that contain non-empty written bodies.

## 2. Recalculation Engine (`RecalculateBookMetricsAction`)
```php
public function handle(Book $book): Book
{
    $approvedQuery = $book->reviews()->where('status', ReviewStatus::Approved);

    $ratingCount = (int) (clone $approvedQuery)->count();
    $average = $ratingCount > 0
        ? round((float) (clone $approvedQuery)->avg('rating'), 2)
        : 0.00;

    $reviewCount = (int) (clone $approvedQuery)
        ->whereNotNull('body')
        ->where('body', '!=', '')
        ->count();

    $book->updateQuietly([
        'rating_count' => $ratingCount,
        'rating_average' => $average,
        'review_count' => $reviewCount,
    ]);

    return $book;
}
```

## 3. Automatic Synchronization Hooks
The calculation is invoked automatically via:
1. `Review::saved` Eloquent model event.
2. `Review::deleted` Eloquent model event.
3. Filament row actions (`approve`, `reject`, `spam`).
4. Filament bulk actions (`approve_selected`, `reject_selected`, `spam_selected`).
5. Direct execution inside `SubmitReviewAction`.

This multi-layered hook ensures that no code path can alter a review's rating or status without keeping parent book counters perfectly synchronized.
