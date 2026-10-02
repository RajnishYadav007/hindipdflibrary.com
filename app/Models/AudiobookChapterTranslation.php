<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AudiobookChapterTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'audiobook_chapter_id',
        'locale',
        'title',
        'description',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(AudiobookChapter::class, 'audiobook_chapter_id');
    }
}
