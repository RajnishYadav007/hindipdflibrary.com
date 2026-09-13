<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GenreTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'genre_id',
        'locale',
        'name',
        'description',
        'seo_title',
        'seo_description',
    ];

    /**
     * Parent genre model.
     */
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }
}
