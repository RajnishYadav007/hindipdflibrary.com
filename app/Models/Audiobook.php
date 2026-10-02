<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Audiobook extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'book_id',
        'language_id',
        'slug',
        'author_name',
        'narrator',
        'duration_seconds',
        'status',
        'visibility',
        'submission_type',
        'submitted_by',
        'approved_by',
        'approved_at',
        'admin_notes',
        'published_at',
        'source_type',
        'source_url',
        'external_audio_url',
        'copyright_status',
        'license_type',
        'rights_holder',
        'permission_notes',
        'is_featured',
        'is_free',
        'sort_order',
        'play_count',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'is_featured' => 'boolean',
            'is_free' => 'boolean',
            'sort_order' => 'integer',
            'play_count' => 'integer',
            'approved_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    protected function getTranslationModelName(): string
    {
        return AudiobookTranslation::class;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->localized()?->title;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->localized()?->description;
    }

    public function getAuthorNameAttribute(): ?string
    {
        if (! empty($this->attributes['author_name'])) {
            return $this->attributes['author_name'];
        }

        return $this->book?->authors->pluck('name')->join(', ');
    }

    public function getCoverUrlAttribute(): ?string
    {
        $coverMedia = $this->media()->where('collection_name', 'cover')->first();
        if ($coverMedia) {
            return Storage::disk($coverMedia->disk)->url($coverMedia->file_name);
        }

        if ($this->book) {
            return $this->book->cover_url;
        }

        return null;
    }

    public function getAudioUrlAttribute(): ?string
    {
        $audioMedia = $this->media()->where('collection_name', 'audio')->first();
        if ($audioMedia) {
            return route('audio.stream', ['uuid' => $audioMedia->uuid ?? $audioMedia->id]);
        }

        return $this->external_audio_url;
    }

    public function getFormattedDurationAttribute(): ?string
    {
        $seconds = $this->duration_seconds;

        if ($seconds === null && $this->relationLoaded('chapters') && $this->chapters->isNotEmpty()) {
            $chapterSum = $this->chapters->sum('duration_seconds');
            if ($chapterSum > 0) {
                $seconds = $chapterSum;
            }
        }

        if ($seconds === null || $seconds <= 0) {
            return null;
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%02d:%02d', $minutes, $secs);
    }

    /**
     * Get the normalized playable tracks for the player.
     * Supports:
     * - Case A: Chapter-based audiobook (published chapters with audio).
     * - Case B: Single-track audiobook (0 chapters, parent audio exists).
     * - Case D: Returns empty collection when no playable audio source exists.
     *
     * @return Collection<int, array{
     *     id: int|string,
     *     chapter_number: int,
     *     title: string,
     *     audio_url: ?string,
     *     duration_seconds: ?int,
     *     formatted_duration: ?string
     * }>
     */
    public function getPlayableTracksAttribute(): Collection
    {
        $chapters = $this->relationLoaded('publishedChapters')
            ? $this->publishedChapters
            : $this->publishedChapters()->get();

        if ($chapters->isNotEmpty()) {
            return $chapters->map(fn ($c) => [
                'id' => $c->id,
                'chapter_number' => $c->chapter_number,
                'title' => $c->title,
                'audio_url' => $c->audio_url,
                'duration_seconds' => $c->duration_seconds,
                'formatted_duration' => $c->formatted_duration,
            ]);
        }

        $parentAudioUrl = $this->audio_url;
        if (! empty($parentAudioUrl)) {
            return collect([[
                'id' => 'single',
                'chapter_number' => 1,
                'title' => $this->title ?: 'सम्पूर्ण ऑडियोबुक (Full Audiobook)',
                'audio_url' => $parentAudioUrl,
                'duration_seconds' => $this->duration_seconds,
                'formatted_duration' => $this->formatted_duration,
            ]]);
        }

        return collect([]);
    }

    /**
     * Check if the audiobook has at least one valid playable audio source.
     */
    public function hasPlayableSource(): bool
    {
        return $this->playable_tracks->contains(fn ($t) => ! empty($t['audio_url']));
    }

    public function translations(): HasMany
    {
        return $this->hasMany(AudiobookTranslation::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(AudiobookChapter::class)->orderBy('chapter_number', 'asc');
    }

    public function publishedChapters(): HasMany
    {
        return $this->hasMany(AudiobookChapter::class)
            ->where('status', 'published')
            ->orderBy('chapter_number', 'asc');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('visibility', 'public');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopePendingReview(Builder $query): Builder
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('submitted_by', $userId);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Get user-facing moderation feedback safely.
     * Internal notes are never exposed on pending/draft items.
     */
    public function getUserFacingFeedbackAttribute(): ?string
    {
        if (in_array($this->status, ['rejected', 'revision_required'])) {
            return $this->admin_notes ? htmlspecialchars(trim((string) $this->admin_notes), ENT_QUOTES, 'UTF-8') : null;
        }

        return null;
    }
}
