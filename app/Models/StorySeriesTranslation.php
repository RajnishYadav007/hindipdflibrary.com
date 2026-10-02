<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorySeriesTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_series_id',
        'locale',
        'title',
        'description',
        'seo_title',
        'seo_description',
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(StorySeries::class, 'story_series_id');
    }
}
