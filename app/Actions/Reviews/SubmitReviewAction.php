<?php

namespace App\Actions\Reviews;

use App\Actions\Books\RecalculateBookMetricsAction;
use App\Enums\ReviewStatus;
use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use App\Services\Analytics\AnalyticsService;
use App\Support\Analytics\AnalyticsEvent;
use InvalidArgumentException;

class SubmitReviewAction
{
    public function __construct(
        protected RecalculateBookMetricsAction $recalculateMetrics
    ) {}

    /**
     * Submit or update a user's rating and review for a published book.
     *
     * @throws InvalidArgumentException
     */
    public function handle(
        User $user,
        Book $book,
        int $rating,
        ?string $title = null,
        ?string $body = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): Review {
        if (! $book->isPublished()) {
            throw new InvalidArgumentException('Reviews can only be submitted for published books.');
        }

        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException('Rating must be an integer between 1 and 5.');
        }

        $sanitizedTitle = $title !== null ? trim($title) : null;
        $sanitizedBody = $body !== null ? trim($body) : null;

        // Find existing review or instantiate new
        $review = Review::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        if ($review) {
            $review->rating = $rating;
            $review->title = $sanitizedTitle ?: null;
            $review->body = $sanitizedBody ?: null;
            $review->status = ReviewStatus::Pending;
            if ($ip) {
                $review->ip_hash = hash('sha256', $ip);
            }
            if ($userAgent) {
                $review->user_agent_hash = hash('sha256', $userAgent);
            }
            $review->save();
        } else {
            $review = Review::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'rating' => $rating,
                'title' => $sanitizedTitle ?: null,
                'body' => $sanitizedBody ?: null,
                'status' => ReviewStatus::Pending,
                'is_verified' => true,
                'ip_hash' => $ip ? hash('sha256', $ip) : null,
                'user_agent_hash' => $userAgent ? hash('sha256', $userAgent) : null,
            ]);
        }

        // Recalculate metrics for the book
        $this->recalculateMetrics->handle($book);

        // Record first-party analytics events
        $analytics = app(AnalyticsService::class);
        $analytics->record(AnalyticsEvent::RATING_SUBMITTED, [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'metadata' => ['rating' => $rating],
        ]);

        if ($sanitizedTitle || $sanitizedBody) {
            $analytics->record(AnalyticsEvent::REVIEW_SUBMITTED, [
                'user_id' => $user->id,
                'book_id' => $book->id,
            ]);
        }

        return $review;
    }
}
