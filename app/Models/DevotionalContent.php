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

class DevotionalContent extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'type', // chalisa, aarti
        'language_id',
        'slug',
        'deity_name',
        'author_name',
        'status',
        'submitted_by',
        'approved_by',
        'approved_at',
        'admin_notes',
        'published_at',
        'copyright_status',
        'license_type',
        'rights_holder',
        'permission_notes',
        'source_url',
        'sort_order',
        'view_count',
        'listen_count',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'view_count' => 'integer',
            'listen_count' => 'integer',
            'approved_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    protected function getTranslationModelName(): string
    {
        return DevotionalContentTranslation::class;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->localized()?->title;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->localized()?->description;
    }

    public function getContentAttribute(): ?string
    {
        return $this->localized()?->content;
    }

    public function getCoverUrlAttribute(): ?string
    {
        $coverMedia = $this->media()->where('collection_name', 'cover')->first();
        if ($coverMedia) {
            return Storage::disk($coverMedia->disk)->url($coverMedia->file_name);
        }

        return null;
    }

    public function isChalisa(): bool
    {
        return $this->type === 'chalisa';
    }

    public function isAarti(): bool
    {
        return $this->type === 'aarti';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function translations(): HasMany
    {
        return $this->hasMany(DevotionalContentTranslation::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
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

    public function scopeChalisas(Builder $query): Builder
    {
        return $query->where('type', 'chalisa');
    }

    public function scopeAartis(Builder $query): Builder
    {
        return $query->where('type', 'aarti');
    }

    public function scopeForDeity(Builder $query, string $deity): Builder
    {
        return $query->where('deity_name', 'like', "%{$deity}%");
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
