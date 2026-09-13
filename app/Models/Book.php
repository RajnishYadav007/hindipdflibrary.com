<?php

namespace App\Models;

use App\Enums\BookStatus;
use App\Models\Concerns\HasTranslations;
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
}
