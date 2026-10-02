<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoryEpisode extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'story_series_id',
        'episode_number',
        'narration_type', // tts, uploaded, none
        'duration_seconds',
        'external_audio_url',
        'status', // draft, pending_review, published, archived
        'sort_order',
        'view_count',
        'listen_count',
    ];

    protected function casts(): array
    {
        return [
            'episode_number' => 'integer',
            'duration_seconds' => 'integer',
            'sort_order' => 'integer',
            'view_count' => 'integer',
            'listen_count' => 'integer',
        ];
    }

    protected function getTranslationModelName(): string
    {
        return StoryEpisodeTranslation::class;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->localized()?->title ?? "Episode {$this->episode_number}";
    }

    public function getSlugAttribute(): ?string
    {
        return $this->localized()?->slug ?? "episode-{$this->episode_number}";
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->localized()?->description;
    }

    public function getContentAttribute(): ?string
    {
        return $this->localized()?->content;
    }

    public function getAudioUrlAttribute(): ?string
    {
        $media = $this->media()->where('collection_name', 'audio')->first();
        if ($media) {
            return route('audio.stream', ['uuid' => $media->uuid ?? $media->id]);
        }

        return $this->external_audio_url;
    }

    public function getHasUploadedAudioAttribute(): bool
    {
        return $this->narration_type === 'uploaded' &&
            ($this->media()->where('collection_name', 'audio')->exists() || ! empty($this->external_audio_url));
    }

    public function getFormattedDurationAttribute(): ?string
    {
        if ($this->duration_seconds === null) {
            return null;
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function translations(): HasMany
    {
        return $this->hasMany(StoryEpisodeTranslation::class);
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(StorySeries::class, 'story_series_id');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
