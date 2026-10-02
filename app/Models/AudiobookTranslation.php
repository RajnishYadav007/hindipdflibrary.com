<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AudiobookTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'audiobook_id',
        'locale',
        'title',
        'description',
        'seo_title',
        'seo_description',
    ];

    public function audiobook(): BelongsTo
    {
        return $this->belongsTo(Audiobook::class);
    }
}
