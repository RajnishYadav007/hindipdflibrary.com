<?php

namespace App\Models;

use App\Enums\AuthorRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BookAuthor extends Pivot
{
    protected $table = 'book_authors';

    protected $fillable = [
        'book_id',
        'author_id',
        'role',
    ];

    protected function casts(): array
    {
        return [
            'role' => AuthorRole::class,
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
