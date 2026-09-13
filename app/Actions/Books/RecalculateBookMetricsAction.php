<?php

namespace App\Actions\Books;

use App\Enums\ReviewStatus;
use App\Models\Book;

class RecalculateBookMetricsAction
{
    /**
     * Recalculate and persist cached rating and review metrics for the book.
     * Only approved reviews contribute to public metrics.
     */
    public function handle(Book $book): Book
    {
        $approvedQuery = $book->reviews()->where('status', ReviewStatus::Approved);

        $ratingCount = (int) (clone $approvedQuery)->count();
        $average = $ratingCount > 0
            ? round((float) (clone $approvedQuery)->avg('rating'), 2)
            : 0.00;

        // Count reviews that contain non-empty written feedback/body
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
}
