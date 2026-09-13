<?php

namespace Database\Factories;

use App\Models\Genre;
use App\Models\GenreTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GenreTranslation>
 */
class GenreTranslationFactory extends Factory
{
    protected $model = GenreTranslation::class;

    public function definition(): array
    {
        $name = fake()->word();

        return [
            'genre_id' => Genre::factory(),
            'locale' => 'en',
            'name' => ucfirst($name),
            'description' => fake()->sentence(),
            'seo_title' => ucfirst($name) . ' Genre Books - Hindi PDF Library',
            'seo_description' => "Discover top rated {$name} literature and Hindi digital books.",
        ];
    }
}
