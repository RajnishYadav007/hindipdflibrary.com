<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Books available in this language.
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_languages')->withTimestamps();
    }

    /**
     * Books originally written in this language.
     */
    public function originalBooks(): HasMany
    {
        return $this->hasMany(Book::class, 'original_language_id');
    }
}
