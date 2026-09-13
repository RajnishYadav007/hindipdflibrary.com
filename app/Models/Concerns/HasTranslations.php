<?php

namespace App\Models\Concerns;

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
}
