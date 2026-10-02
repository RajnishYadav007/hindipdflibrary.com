<?php

namespace App\Observers;

use App\Enums\BookStatus;
use App\Models\Audiobook;
use App\Models\Book;
use App\Models\DevotionalContent;
use App\Models\StorySeries;
use App\Services\Contributor\ContributorRewardService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContentPublicationObserver
{
    public function __construct(
        protected ContributorRewardService $rewardService
    ) {}

    /**
     * Handle the saved event for literature content.
     */
    public function saved(Model $model): void
    {
        // Only observe supported primary literature models
        if (! $this->isSupportedModel($model)) {
            return;
        }

        $isPublished = $this->isModelPublished($model);
        $wasStatusChanged = $model->wasChanged('status');
        $wasRecentlyCreated = $model->wasRecentlyCreated;

        if ($wasRecentlyCreated && $isPublished) {
            $this->safelyAwardPoints($model);
        } elseif ($wasStatusChanged) {
            if ($isPublished) {
                $this->safelyAwardPoints($model);
            } elseif ($this->wasPreviouslyPublished($model)) {
                $this->safelyRevokePoints($model, 'स्थिति बदलकर अप्रकाशित/समीक्षाधीन की गई');
            }
        }
    }

    /**
     * Handle the deleted event for literature content.
     */
    public function deleted(Model $model): void
    {
        if (! $this->isSupportedModel($model)) {
            return;
        }

        if ($this->isModelPublished($model)) {
            $this->safelyRevokePoints($model, 'साहित्य हटाया गया');
        }
    }

    /**
     * Award points safely with publication failure isolation.
     */
    protected function safelyAwardPoints(Model $model): void
    {
        try {
            $this->rewardService->awardForPublishedContent($model);
        } catch (Throwable $e) {
            // Reward processing failure must never break publication transaction
            Log::error('Contributor points award failed on publication: '.$e->getMessage(), [
                'content_type' => get_class($model),
                'content_id' => $model->getKey(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Revoke points safely with publication failure isolation.
     */
    protected function safelyRevokePoints(Model $model, string $reason): void
    {
        try {
            $this->rewardService->revokeForUnpublishedContent($model, $reason);
        } catch (Throwable $e) {
            Log::error('Contributor points revocation failed: '.$e->getMessage(), [
                'content_type' => get_class($model),
                'content_id' => $model->getKey(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Check if the model is one of the 4 supported primary literature types.
     */
    protected function isSupportedModel(Model $model): bool
    {
        return $model instanceof Book
            || $model instanceof Audiobook
            || $model instanceof DevotionalContent
            || $model instanceof StorySeries;
    }

    /**
     * Determine whether the model is in published status.
     */
    protected function isModelPublished(Model $model): bool
    {
        if ($model instanceof Book) {
            return $model->status === BookStatus::Published
                || ($model->status instanceof \BackedEnum ? $model->status->value === 'published' : (string) $model->status === 'published');
        }

        return $model->status instanceof \BackedEnum ? $model->status->value === 'published' : (string) $model->status === 'published';
    }

    /**
     * Check if model was previously published prior to the latest change.
     */
    protected function wasPreviouslyPublished(Model $model): bool
    {
        $originalStatus = $model->getOriginal('status');
        if ($originalStatus instanceof BookStatus) {
            return $originalStatus === BookStatus::Published;
        }

        if ($originalStatus instanceof \BackedEnum) {
            return $originalStatus->value === 'published';
        }

        return (string) $originalStatus === 'published';
    }
}
