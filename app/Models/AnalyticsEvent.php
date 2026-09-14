<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    use HasFactory;

    /**
     * Disable updated_at and created_at timestamps (we use occurred_at).
     */
    public $timestamps = false;

    protected $fillable = [
        'event_name',
        'user_id',
        'visitor_id',
        'session_id',
        'book_id',
        'author_id',
        'category_id',
        'search_query',
        'search_result_count',
        'source',
        'country',
        'device_type',
        'browser',
        'operating_system',
        'url_path',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
        'search_result_count' => 'integer',
        'user_id' => 'integer',
        'book_id' => 'integer',
        'author_id' => 'integer',
        'category_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
