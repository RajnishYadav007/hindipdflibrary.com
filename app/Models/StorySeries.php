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
use Illuminate\Support\Facades\Storage;

class StorySeries extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $table = 'story_series';

    protected $fillable = [
        'category_id',
        'language_id',
        'slug',
        'story_type', // 'children', 'general'
        'classification', // 'mythological', 'folk', 'historical', 'inspirational', 'social', 'regional'
        'age_group', // '3-5', '6-8', '9-12', 'all'
        'status',
        'is_featured',
        'submitted_by',
        'approved_by',
        'approved_at',
        'admin_notes',
        'published_at',
        'copyright_status',
        'license_type',
        'rights_holder',
        'permission_notes',
        'sort_order',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'view_count' => 'integer',
            'approved_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    protected function getTranslationModelName(): string
    {
        return StorySeriesTranslation::class;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->localized()?->title;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->localized()?->description;
    }

    public function getCoverUrlAttribute(): ?string
    {
        $coverMedia = $this->media()->where('collection_name', 'cover')->first();
        if ($coverMedia) {
            return Storage::disk($coverMedia->disk)->url($coverMedia->file_name);
        }

        return null;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function translations(): HasMany
    {
        return $this->hasMany(StorySeriesTranslation::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(StoryEpisode::class)->orderBy('episode_number', 'asc');
    }

    public function publishedEpisodes(): HasMany
    {
        return $this->hasMany(StoryEpisode::class)
            ->where('status', 'published')
            ->orderBy('episode_number', 'asc');
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
        return $query->where('status', 'published');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeForAge(Builder $query, string $age): Builder
    {
        return $query->where('age_group', $age);
    }

    public function scopePendingReview(Builder $query): Builder
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('submitted_by', $userId);
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
