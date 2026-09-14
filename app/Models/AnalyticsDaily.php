<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsDaily extends Model
{
    use HasFactory;

    protected $table = 'analytics_daily';

    protected $fillable = [
        'date',
        'event_name',
        'book_id',
        'source',
        'country',
        'device_type',
        'event_count',
        'unique_visitors',
        'unique_sessions',
    ];

    protected $casts = [
        'date' => 'date',
        'event_count' => 'integer',
        'unique_visitors' => 'integer',
        'unique_sessions' => 'integer',
        'book_id' => 'integer',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
