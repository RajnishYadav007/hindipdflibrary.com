<?php

namespace App\Models;

use App\Actions\Books\RecalculateBookMetricsAction;
use App\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'rating',
        'title',
        'body',
        'status',
        'is_verified',
        'ip_hash',
        'user_agent_hash',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'status' => ReviewStatus::class,
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Book being reviewed.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * User who wrote the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saved(function (Review $review): void {
            if ($review->book) {
                app(RecalculateBookMetricsAction::class)->handle($review->book);
            }
        });

        static::deleted(function (Review $review): void {
            if ($review->book) {
                app(RecalculateBookMetricsAction::class)->handle($review->book);
            }
        });
    }

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ReviewStatus::Approved);
    }

    /**
     * Scope a query to only include pending reviews.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ReviewStatus::Pending);
    }

    /**
     * Scope a query to only include rejected reviews.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', ReviewStatus::Rejected);
    }

    /**
     * Scope a query to only include spam reviews.
     */
    public function scopeSpam(Builder $query): Builder
    {
        return $query->where('status', ReviewStatus::Spam);
    }
}
