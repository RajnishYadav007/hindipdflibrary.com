<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasTranslations
{
    /**
     * Get all translations for the model.
     */
    abstract public function translations(): HasMany;

    /**
     * Get the translation for the given or current locale.
     */
    public function translation(?string $locale = null): HasOne
    {
        $locale = $locale ?? app()->getLocale();

        return $this->hasOne($this->getTranslationModelName())
            ->where('locale', $locale);
    }

    /**
     * Get the translation model class name.
     */
    abstract protected function getTranslationModelName(): string;

    /**
     * Get the resolved translation for the specified or active locale,
     * utilizing eager-loaded translations to prevent N+1 queries,
     * with predictable fallback to English and then first available.
     */
    public function localized(?string $locale = null): ?Model
    {
        $locale = $locale ?? app()->getLocale();
        $fallback = config('localization.fallback_locale', 'en');

        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('locale', $locale)
                ?? $this->translations->firstWhere('locale', $fallback)
                ?? $this->translations->first();
        }

        return $this->translations()->where('locale', $locale)->first()
            ?? $this->translations()->where('locale', $fallback)->first()
            ?? $this->translations()->first();
    }

    /**
     * Check if a translation exists for the given locale.
     */
    public function hasTranslation(string $locale): bool
    {
        if ($this->relationLoaded('translations')) {
            return $this->translations->contains('locale', $locale);
        }

        return $this->translations()->where('locale', $locale)->exists();
    }

    /**
     * Get an array of all available locale codes for this entity.
     */
    public function availableLocales(): array
    {
        if ($this->relationLoaded('translations')) {
            return $this->translations->pluck('locale')->unique()->values()->all();
        }

        return $this->translations()->pluck('locale')->unique()->values()->all();
    }
}
