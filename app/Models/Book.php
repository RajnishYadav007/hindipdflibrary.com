<?php

namespace App\Models;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Models\Concerns\HasTranslations;
use App\Services\Archive\ArchiveOrgService;
use App\Support\Security\SafeUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'user_id',
        'slug',
        'status',
        'publication_year',
        'publisher',
        'isbn',
        'isbn13',
        'pages',
        'edition',
        'original_language_id',
        'rating_average',
        'rating_count',
        'review_count',
        'view_count',
        'content_type',
        'deity',
        'source_url',
        'license_type',
        'rights_holder',
        'copyright_status',
        'permission_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookStatus::class,
            'copyright_status' => CopyrightStatus::class,
            'publication_year' => 'integer',
            'pages' => 'integer',
            'rating_average' => 'decimal:2',
            'rating_count' => 'integer',
            'review_count' => 'integer',
            'view_count' => 'integer',
        ];
    }

    protected function getTranslationModelName(): string
    {
        return BookTranslation::class;
    }

    /**
     * Get the resolved title for the active locale.
     */
    public function getTitleAttribute(): ?string
    {
        return $this->localized()?->title ?? $this->slug;
    }

    /**
     * Get the resolved description for the active locale.
     */
    public function getDescriptionAttribute(): ?string
    {
        return $this->localized()?->description;
    }

    /**
     * Get the cover image URL or fall back to null.
     */
    public function getCoverUrlAttribute(): ?string
    {
        $coverMedia = $this->relationLoaded('media')
            ? ($this->media->firstWhere('collection_name', 'cover') ?? $this->media->filter(fn ($m) => str_contains($m->mime_type ?? '', 'image'))->first())
            : $this->media()->where('collection_name', 'cover')->first();

        if ($coverMedia) {
            return asset('storage/'.$coverMedia->file_name);
        }

        if (! empty($this->source_url)) {
            $archiveService = app(ArchiveOrgService::class);
            $identifier = $archiveService->extractIdentifier($this->source_url);
            if ($identifier) {
                return "https://archive.org/services/img/{$identifier}";
            }
        }

        return null;
    }

    /**
     * Translations for the book.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(BookTranslation::class);
    }

    /**
     * Authors / contributors associated with the book.
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'book_authors')
            ->using(BookAuthor::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Categories the book belongs to.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'book_categories')->withTimestamps();
    }

    /**
     * Genres the book belongs to.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'book_genres')->withTimestamps();
    }

    /**
     * Languages in which the book is available.
     */
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'book_languages')->withTimestamps();
    }

    /**
     * User who contributed or submitted the book.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Alias for uniform submitter relation across all content systems.
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get user-facing moderation feedback safely.
     * Internal notes are never exposed on pending/draft items.
     */
    public function getUserFacingFeedbackAttribute(): ?string
    {
        $statusVal = is_object($this->status) ? ($this->status->value ?? (string) $this->status) : (string) $this->status;
        if (in_array($statusVal, ['rejected', 'revision_required'])) {
            return $this->permission_notes ? htmlspecialchars(trim((string) $this->permission_notes), ENT_QUOTES, 'UTF-8') : null;
        }

        return null;
    }

    /**
     * Original language of the book.
     */
    public function originalLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'original_language_id');
    }

    /**
     * Reviews and ratings for the book.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Users who favorited/bookmarked the book.
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Media assets for the book (e.g. cover images, PDF documents).
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    /**
     * SEO metadata for the book.
     */
    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    /**
     * Scope a query to only include published books.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', BookStatus::Published);
    }

    /**
     * Check if the book is published.
     */
    public function isPublished(): bool
    {
        return $this->status === BookStatus::Published;
    }

    /**
     * Whether the platform is legally permitted to store a private PDF for this book.
     */
    public function canHostPdf(): bool
    {
        if ($this->copyright_status instanceof CopyrightStatus) {
            return $this->copyright_status->canHostPdf();
        }

        if (! empty($this->copyright_status)) {
            $val = is_object($this->copyright_status) && isset($this->copyright_status->value)
                ? $this->copyright_status->value
                : (string) $this->copyright_status;

            return CopyrightStatus::tryFrom($val)?->canHostPdf() ?? false;
        }

        // When copyright_status is not explicitly set, permit hosting if local PDF media is attached
        return $this->relationLoaded('media')
            ? $this->media->contains(fn ($m) => $m->collection_name === 'pdf' || str_contains($m->mime_type ?? '', 'pdf'))
            : $this->media()->where('collection_name', 'pdf')->orWhere('mime_type', 'like', '%pdf%')->exists();
    }

    /**
     * Whether readers are legally permitted to download the PDF file directly.
     */
    public function canDownloadPdf(): bool
    {
        return $this->isPublished() && $this->canHostPdf();
    }

    /**
     * Whether this book is strictly an external referral/discovery record without local hosting.
     */
    public function hasExternalSourceOnly(): bool
    {
        if ($this->copyright_status instanceof CopyrightStatus) {
            return $this->copyright_status === CopyrightStatus::ExternalSourceOnly;
        }

        return $this->copyright_status === 'external_source_only';
    }

    /**
     * Get validated, safe source URL. Returns null if unsafe or invalid.
     */
    public function getSafeSourceUrl(): ?string
    {
        return SafeUrl::sanitize($this->source_url);
    }

    /**
     * Determine if this content item is a devotional prayer (Chalisa, Aarti, Stotra, etc.).
     */
    public function isPrayer(): bool
    {
        if (in_array($this->content_type, ['chalisa', 'aarti', 'stotra', 'mantra', 'suktam'], true)) {
            return true;
        }

        return ! empty($this->localized()?->lyrics);
    }

    /**
     * Determine if this content item is a standard book / scripture.
     */
    public function isBook(): bool
    {
        return ! $this->isPrayer();
    }

    /**
     * Human-friendly label for the content type.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->content_type) {
            'chalisa' => 'चालीसा (Chalisa)',
            'aarti' => 'आरती (Aarti)',
            'stotra' => 'स्तोत्र / पाठ (Stotra)',
            'mantra' => 'मंत्र (Mantra)',
            default => 'ग्रंथ / पुस्तक (Book)',
        };
    }

    /**
     * Human-friendly label for the divine entity/deity.
     */
    public function getDeityLabelAttribute(): ?string
    {
        return match ($this->deity) {
            'hanuman' => 'श्री हनुमान जी (Lord Hanuman)',
            'shiva' => 'भगवान शिव (Lord Shiva)',
            'ram' => 'प्रभु श्री राम (Lord Rama)',
            'krishna' => 'भगवान श्री कृष्ण (Lord Krishna)',
            'ganesh' => 'श्री गणेश (Lord Ganesha)',
            'durga' => 'मां दुर्गा (Maa Durga)',
            'lakshmi' => 'माता लक्ष्मी (Maa Lakshmi)',
            'saraswati' => 'माता सरस्वती (Maa Saraswati)',
            'vishnu' => 'भगवान विष्णु (Lord Vishnu)',
            'surya' => 'भगवान सूर्य (Lord Surya)',
            'vedas' => 'वेद एवं उपनिषद् (Vedas & Upanishads)',
            default => $this->deity ? ucfirst($this->deity) : null,
        };
    }
}
