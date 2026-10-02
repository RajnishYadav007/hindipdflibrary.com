<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryEpisodeTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_episode_id',
        'locale',
        'title',
        'slug',
        'description',
        'content',
        'seo_title',
        'seo_description',
    ];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(StoryEpisode::class, 'story_episode_id');
    }
}
