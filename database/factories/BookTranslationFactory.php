<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookTranslation>
 */
class BookTranslationFactory extends Factory
{
    protected $model = BookTranslation::class;

    public function definition(): array
    {
        $title = ucfirst(fake()->words(3, true));

        return [
            'book_id' => Book::factory(),
            'locale' => 'en',
            'title' => $title,
            'short_description' => fake()->sentence(12),
            'description' => fake()->paragraphs(2, true),
            'summary' => fake()->paragraph(),
            'key_themes' => ['Social Realism', 'Cultural Identity', 'Human Experience'],
            'why_read' => 'A timeless literary work exploring human dignity, compassion, and resilience.',
            'seo_title' => "{$title} PDF Download - Hindi PDF Library",
            'seo_description' => "Read or download {$title} PDF for free on Hindi PDF Library. High quality digital edition.",
        ];
    }
}
