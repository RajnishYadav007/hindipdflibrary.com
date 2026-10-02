<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AudiobookChapter extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'audiobook_id',
        'chapter_number',
        'duration_seconds',
        'external_audio_url',
        'source_file_name',
        'mime_type',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'chapter_number' => 'integer',
            'duration_seconds' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    protected function getTranslationModelName(): string
    {
        return AudiobookChapterTranslation::class;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->localized()?->title ?? "Chapter {$this->chapter_number}";
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->localized()?->description;
    }

    public function getAudioUrlAttribute(): ?string
    {
        $media = $this->media()->where('collection_name', 'audio')->first();
        if ($media) {
            return route('audio.stream', ['uuid' => $media->uuid ?? $media->id]);
        }

        return $this->external_audio_url;
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

    public function translations(): HasMany
    {
        return $this->hasMany(AudiobookChapterTranslation::class);
    }

    public function audiobook(): BelongsTo
    {
        return $this->belongsTo(Audiobook::class);
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
