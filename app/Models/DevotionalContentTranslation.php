<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevotionalContentTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'devotional_content_id',
        'locale',
        'title',
        'description',
        'content',
        'seo_title',
        'seo_description',
    ];

    public function devotionalContent(): BelongsTo
    {
        return $this->belongsTo(DevotionalContent::class);
    }
}
