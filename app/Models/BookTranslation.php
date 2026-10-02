<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'locale',
        'title',
        'short_description',
        'description',
        'summary',
        'key_themes',
        'why_read',
        'lyrics',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'key_themes' => 'array',
        ];
    }

    /**
     * Parent book model.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
