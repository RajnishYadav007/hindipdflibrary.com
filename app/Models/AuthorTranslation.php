<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'locale',
        'name',
        'biography',
        'seo_title',
        'seo_description',
    ];

    /**
     * Parent author model.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
