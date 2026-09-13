<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\AuthorTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuthorTranslation>
 */
class AuthorTranslationFactory extends Factory
{
    protected $model = AuthorTranslation::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'author_id' => Author::factory(),
            'locale' => 'en',
            'name' => $name,
            'biography' => fake()->paragraph(),
            'seo_title' => "Books by {$name} - Biography & PDFs",
            'seo_description' => "Explore digital books, essays, and stories written by {$name}.",
        ];
    }
}
